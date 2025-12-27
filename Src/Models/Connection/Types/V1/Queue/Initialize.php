<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\Queue;

abstract class Initialize extends Alpha
{
	protected $_queueName=null;
	protected $_queueRxId=null;
	protected $_queueTxId=null;
	protected $_queueObj=null;
	
	public function setQueueName($val)
	{
		$this->isStrMax($val, 64, true);
		$val	= trim($val);
		$this->isStrMin($val, 1, true);
		$this->_queueName	= $val;
		return $this;
	}
	public function getQueueName()
	{
		return $this->_queueName;
	}
	public function setQueueRxId($val)
	{
		if ($val !== null) {
			$this->isUsign32Int($val, true);
		}
		$this->_queueRxId	= $val;
		return $this;
	}
	public function getQueueRxId()
	{
		return $this->_queueRxId;
	}
	public function setQueueTxId($val)
	{
		if ($val !== null) {
			$this->isUsign32Int($val, true);
		}
		$this->_queueTxId	= $val;
		return $this;
	}
	public function getQueueTxId()
	{
		return $this->_queueTxId;
	}
	public function initialize()
	{
		if ($this->isInit() === false) {
			
			try {
				
				
				if ($this->getQueueName() === null) {
					throw new \Exception("Cannot initialize without a queue name", 1111);
				}
				
				$this->_queueObj	= \MTM\Queues\Factories::getMessages()->getSystemFive()->getQueue($this->getQueueName(), "0666");
	
				$loopObj			= \MTM\Events\Facts::getLoops()->getV1();
				$evObj				= $loopObj->addEvent($this, "receiveMessage");
				
				$errCb				= $this->getErrorCb();
				if ($errCb !== null) {
					$evObj->setErrorCb($errCb[0], $errCb[1]);
				}
				
				$evObj->setNextExecuteDelay(0);
				$this->_isInit		= true;
			
			} catch (\Exception $e) {
				$this->terminate();
				throw $e;
			}
		}
		return $this;
	}
}