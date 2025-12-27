<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1\Relay;

abstract class Response extends Request
{
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
	public function sendResponse($throw=true)
	{
		if ($this->getResponseSent() === false) {
			
			try {
				$this->getRxConn()->transmitMessage($this);
				$this->setResponseSent(true);
			} catch (\Exception $e) {
				$this->setDone(true);
				switch ($e->getCode()) {
					default;
					if ($throw === true) {
						throw $e;
					}
					break;
				}
			}
		}
		return $this;
	}
	public function getResponseMsg()
	{
		$msgObj						= new \stdClass();
		
		$msgObj->auth				= new \stdClass();
		$msgObj->auth->hash			= $this->getResponseHash();
		
		$msgObj->data				= $this->getTxData();
		
		$msgObj->error				= new \stdClass();
		$msgObj->error->code		= "";
		$msgObj->error->msg			= "";
		
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
		
		return $msgObj;
	}
}