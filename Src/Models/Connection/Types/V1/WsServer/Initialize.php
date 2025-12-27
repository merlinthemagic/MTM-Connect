<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\WsServer;

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
					throw new \Exception("Cannot initialize, missing binding host", 1111);
				} elseif ($this->getPort() === null) {
					throw new \Exception("Cannot initialize, missing binding port", 1111);
				}
	
				$wsObj		= \MTM\WsSocket\Factories::getServers()->getV1();
				$wsObj->setHost($this->getHost())->setPort($this->getPort())->setProtocol("tls")->setCertificate($this->getCertificate());
				$wsObj->setClientConnectCb($this, "newConnectCb")->setClientConnectAsync(true);
				$this->_wsObj		= $wsObj->initialize();
	
				$loopObj			= \MTM\Events\Facts::getLoops()->getV1();
				$this->_evObj		= $loopObj->addEvent($this, "checkClients");
				
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
	
}