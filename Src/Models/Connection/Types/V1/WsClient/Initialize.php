<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\WsClient;

abstract class Initialize extends Alpha
{
	protected $_evObj=null;
	
	public function initialize()
	{
		if ($this->isInit() === false) {
			
			try {
				
				if ($this->getCertificate() === null) {
					throw new \Exception("Cannot initialize, missing certificate", 1111);
				} elseif ($this->getHost() === null) {
					throw new \Exception("Cannot initialize, missing host", 1111);
				} elseif ($this->getPort() === null) {
					throw new \Exception("Cannot initialize, missing port", 1111);
				}
				
				$wsObj				= \MTM\WsSocket\Factories::getClients()->getV1();
				$wsObj->setHost($this->getHost())->setPort($this->getPort())->setProtocol("tls")->setCertificate($this->getCertificate());
				$wsObj->setConnectCb($this, "socketConnectCb")->setTermCb($this, "socketTermCb");
				$wsObj->connect();
				$this->_wsSock		= $wsObj;
	
				$loopObj			= \MTM\Events\Facts::getLoops()->getV1();
				$this->_evObj		= $loopObj->addEvent($this, "receiveMessage");
				
				$errCb				= $this->getErrorCb();
				if ($errCb !== null) {
					$this->_evObj->setErrorCb($errCb[0], $errCb[1]);
				}
				
				$this->_evObj->setNextExecuteDelay(0);
				$this->_isInit		= true;
				if ($this->_wsSock !== null && $this->_wsSock->getAsyncConnect() === false) {
					//sync connect is being used
					$this->setRxActive(true);
				}
				
			} catch (\Exception $e) {
				$this->terminate();
				throw $e;
			}
		}
		return $this;
	}
	public function socketConnectCb($wsSock)
	{
		if ($this->_wsSock !== null && $this->_wsSock->getAsyncConnect() === true) {
			//a sync connect is being used
			$this->setRxActive(true);
		}
	}
	public function socketTermCb($wsSock)
	{
		$this->terminate();
	}
}