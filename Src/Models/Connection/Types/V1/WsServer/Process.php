<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\WsServer;

abstract class Process extends Initialize
{
	protected $_cliObjs=array();
	
	protected $_checkMax=1500; //max poll delay
	protected $_checkMin=500; //minimum poll delay. This is not polling messages, its purely new clients and pings, can be slower that a normal poll
	protected $_checkIncr=10; //poll delay increment
	protected $_checkCur=10; //current poll delay

	protected $_pingInt=120; //seconds between keep alive pings
	protected $_pingNext=0; //time of next ping
	
	public function checkClients($evObj)
	{
		$cObjs			= $this->getWsObj()->getClients();
		if (count($cObjs) > 0) {
			if ($this->_pingNext < time()) {
				//run keep alive
				$this->_pingNext	= time() + $this->_pingInt;
				foreach ($cObjs as $cObj) {
					try {
						$cObj->ping($this->_pingNext);
					} catch (\Exception $e) {
					}
				}
			}

			$this->_checkCur		= $this->_checkMin;
		} else {
			$this->_checkCur	= $this->_checkMax;
		}
		$evObj->setNextExecuteDelay($this->_checkCur);
	}
	public function newConnectCb($wsSock)
	{
		$wsObj	= new \MTM\Connect\Models\Connection\Types\V1\WsServerClient\Zulu();
		$wsObj->setWsSock($wsSock)->setTermCb($this, "removeServerClientCb");
		
		//mirror the parent, if this is a WsServer in push configuration (process also acts as worker) then all messages must be ingress not relay 
		$wsObj->setRelay($this->isRelay());
		$reqCb		= $this->getRequestCb();
		if ($reqCb !== null) {
			$wsObj->setRequestCb($reqCb[0], $reqCb[1]);
		}
		$respCb		= $this->getResponseCb();
		if ($respCb !== null) {
			$wsObj->setResponseCb($respCb[0], $respCb[1]);
		}
		$errCb		= $this->getErrorCb();
		if ($errCb !== null) {
			$wsObj->setErrorCb($errCb[0], $errCb[1]);
		}

		\MTM\Connect\Facts::getConnections()->setCache($wsObj);
		$wsObj->initialize()->setRxActive(true);
		
		$this->_cliObjs[$wsObj->getGuid()]		= $wsObj;
	}
	public function removeServerClientCb($wsObj)
	{
		if (array_key_exists($wsObj->getGuid(), $this->_cliObjs) === true) {
			unset($this->_cliObjs[$wsObj->getGuid()]);
		}
	}
}