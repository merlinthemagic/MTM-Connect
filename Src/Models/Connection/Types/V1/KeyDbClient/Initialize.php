<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\KeyDbClient;

abstract class Initialize extends Alpha
{
	protected $_evObj=null;
	
	public function initialize()
	{
		if ($this->isInit() === false) {
			
			try {
				
				if ($this->getAuthUsername() === null) {
					throw new \Exception("Cannot initialize, missing auth username", 1111); //not yet used
				} elseif ($this->getAuthPassword() === null) {
					throw new \Exception("Cannot initialize, missing auth password", 1111);
				} elseif ($this->getUsername() === null) {
					throw new \Exception("Cannot initialize, missing username", 1111);
				} elseif ($this->getPassword() === null) {
					throw new \Exception("Cannot initialize, missing password", 1111);
				} elseif ($this->getHost() === null) {
					throw new \Exception("Cannot initialize, missing host", 1111);
				} elseif ($this->getPort() === null) {
					throw new \Exception("Cannot initialize, missing port", 1111);
				}
				
				$keyCtrl			= \MTM\KeyDb\Facts::getClients()->getV1();
				$keyCtrl->setHost($this->getHost())->setPort($this->getPort())->setAuth($this->getAuthPassword());
				$keyCtrl->setTimeout($this->getTimeout());
				$keyCtrl->getCmdPing()->execute(); //connect
				$keyCtrl->enableSubscriptions();
			
				$keyCtrl->getMainSock()->addTerminationCb($this, "keyDbTermCb");
				$this->_keyDbObj	= $keyCtrl;

				$loopObj			= \MTM\Events\Facts::getLoops()->getV1();
				$this->_evObj		= $loopObj->addEvent($this, "receiveMessage");
				
				$errCb				= $this->getErrorCb();
				if ($errCb !== null) {
					$this->_evObj->setErrorCb($errCb[0], $errCb[1]);
				}
				
				$this->_evObj->setNextExecuteDelay(0);
				$this->_isInit		= true;
				$this->setRxActive(true);
				
			} catch (\Exception $e) {
				$this->terminate();
				throw $e;
			}
		}
		return $this;
	}
	public function keyDbTermCb($wsSock)
	{
		$this->terminate();
	}
}