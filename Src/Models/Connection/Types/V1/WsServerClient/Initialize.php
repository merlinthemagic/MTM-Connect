<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\WsServerClient;

abstract class Initialize extends Alpha
{
	protected $_evObj=null;
	
	public function initialize()
	{
		if ($this->isInit() === false) {
			
			try {
				
				$wsSock		= $this->getWsSock();
				if ($wsSock === null) {
					throw new \Exception("Cannnot initialize without a socket", 1111);
				}
				$wsSock->setTermCb($this, "socketTermCb");
	
				$loopObj			= \MTM\Events\Facts::getLoops()->getV1();
				$this->_evObj		= $loopObj->addEvent($this, "receiveMessage");
				
				$errCb				= $this->getErrorCb();
				if ($errCb !== null) {
					$this->_evObj->setErrorCb($errCb[0], $errCb[1]);
				}
				
				$this->_evObj->setNextExecuteDelay(0);
				$this->_isInit		= true;
			
			} catch (\Exception $e) {
				$this->terminate();
				throw $e;
			}
		}
		return $this;
	}
	public function socketTermCb($wsSock)
	{
		$this->terminate();
	}
}