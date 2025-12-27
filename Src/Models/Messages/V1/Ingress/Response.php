<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1\Ingress;

abstract class Response extends Request
{
	public function addData($key=null, $throw=false)
	{
		return $this->addTxData($key, $throw);
	}
	public function sendResponse($throw=true)
	{
		if ($this->getResponseSent() === false) {
			try {
				//RX because data goes back the way it came
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
		$msgObj->auth->hash			= "00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
		
		$msgObj->data				= $this->getTxData();

		$msgObj->error				= new \stdClass();
		$msgObj->error->msg			= "";
		$msgObj->error->code		= "";

		$errObj						= $this->getError();
		if ($errObj !== null) {
			$msgObj->error->code		= $errObj->getCode();
			$appStr						= "(".getenv("app.name")."): ";
			if (strpos($errObj->getMessage(), $appStr) === 0) {
				//dont keep piling on, e.g. back end, then reformatted by front before its sent to client
				$msgObj->error->msg			= $errObj->getMessage();
			} else {
				$msgObj->error->msg			= $appStr.$errObj->getMessage();
			}
		}
		
		$msgObj->head				= new \stdClass();
		$msgObj->head->guid			= $this->getGuid();
		$msgObj->head->hostTime		= time();
		$msgObj->head->runTime		= intval(ceil((\MTM\Utilities\Factories::getTime()->getMicroEpoch(false) - $this->getInitTime()) * 1000));
		$msgObj->head->type			= "response";
		$msgObj->head->version		= $this->getVersion();
		$msgObj						= $this->orderData($msgObj);

		$hash						= hash("sha512", json_encode($msgObj, JSON_UNESCAPED_SLASHES));
		$msgObj->auth->hash			= hash("sha512", $hash.$this->getAuthPepper());
		
		return $msgObj;
	}
}