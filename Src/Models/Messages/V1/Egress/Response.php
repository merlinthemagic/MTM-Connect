<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1\Egress;

abstract class Response extends Request
{
	protected $_rxLoopDelay=25000; //not saturating the CPU
	protected $_respHash=null; //response validation hash
	
	public function setResponseHash($val)
	{
		$this->isSha512($val, true);
		$this->_respHash	= $val;
		return $this;
	}
	public function getResponseHash()
	{
		return $this->_respHash;
	}
	public function getResponse($throw=true)
	{
		if ($this->getResponseReceived() === false) {
			
			$this->sendRequest($throw);
			if ($this->getRequestSent() === true) {
				try {
					$loopObj		= \MTM\Events\Facts::getLoops()->getV1();
					while($this->getResponseReceived() === false && $this->getDone() === false) {
						usleep($this->_rxLoopDelay);
						$loopObj->runOnce();
					}
					
					if ($this->getResponseReceived() === true) {
						$this->authenticateResponse(true);
					}
					
				} catch (\Exception $e) {
					$this->setDone(true);
					if ($this->getError() === null) {
						$this->setError($e);
					}
				}
			}
		}
		if ($throw === true && $this->getError() !== null) {
			throw $this->getError();
		}
		return $this->getRxData();
	}
	public function authenticateResponse($throw=true)
	{
		//insertion order matters php maintains the order, routeros orders alphabetically
		//if not done alphabetically the hashing will mismatch and fail
		
		$msgObj						= new \stdClass();
		$msgObj->auth				= new \stdClass();
		$msgObj->auth->hash			= "00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
		
		$msgObj->data				= $this->getRxData();
		
		$msgObj->error				= new \stdClass();
		$msgObj->error->msg			= "";
		$msgObj->error->code		= "";
		
		$errObj						= $this->getError();
		if ($errObj !== null) {
			$msgObj->error->code		= $errObj->getCode();
			$msgObj->error->msg			= $errObj->getMessage();
		}
		
		$msgObj->head				= new \stdClass();
		$msgObj->head->guid			= $this->getGuid();
		$msgObj->head->hostTime		= $this->getHostTime();
		$msgObj->head->runTime		= $this->getRunTime();
		$msgObj->head->type			= "response";
		$msgObj->head->version		= $this->getVersion();
		$msgObj						= $this->orderData($msgObj);
		
		$hash						= hash("sha512", json_encode($msgObj, JSON_UNESCAPED_SLASHES));
		$calcHash					= hash("sha512", $hash.$this->getAuthPepper());
		if ($this->getResponseHash() === $calcHash) {
			return true;
		} elseif ($throw === true) {
			throw new \Exception("Response failed integrity check", 1111);
		} else {
			return false;
		}
	}
}