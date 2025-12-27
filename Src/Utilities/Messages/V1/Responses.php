<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Utilities\Messages\V1;

abstract class Responses extends Requests
{
	protected function parseResponse($connObj, $rawMsg)
	{
		if (
			$connObj instanceof \MTM\Connect\Models\Connection\Types\V1\WsClient\Zulu === false
			&& $connObj instanceof \MTM\Connect\Models\Connection\Types\V1\Queue\Zulu === false
		) {
			throw new \Exception("Not handled for the type of connection provided: '".get_class($connObj)."'", 1111);
		}
		
		$this->stdPropsExist($rawMsg, array("head", "auth", "data", "error"), true);
		$headObj	= $rawMsg->head;
		$authObj	= $rawMsg->auth;
		$errObj		= $rawMsg->error;
		$dataObj	= $rawMsg->data;
		
		$this->stdPropsExist($headObj, array("guid"), true);
		
		$msgObj		= $this->shiftFromRsvpCacheByGuid($headObj->guid, false);
		if ($msgObj === null) {
			throw new \Exception("The message being responded to, does not exist", 88702);
		}
		
		try {
			
			$this->stdPropsExist($errObj, array("msg", "code"), true);
			
			if ($errObj->msg !== "" || $errObj->code !== "") {
				//if the datatype is off here, we fail when hashing the message again
				$errMsg		= "";
				$errCode	= 9999;
				if ($this->isStr($errObj->msg, false) === true) {
					$errMsg		= $errObj->msg;
				}
				if ($this->isUsign32int($errObj->code, false) === true) {
					$errCode		= $errObj->code;
				}
				$msgObj->setError(new \Exception($errMsg, $errCode));
			}
			
			$this->stdPropsExist($authObj, array("hash"), true);
			$this->isStdClass($dataObj, true);
			if ($msgObj instanceof \MTM\Connect\Models\Messages\V1\Relay\Zulu === true) {
				$msgObj->setResponseHash($authObj->hash);
				foreach ($dataObj as $key => $val) {
					$msgObj->addTxData($key, $val);
				}
			} elseif ($msgObj instanceof \MTM\Connect\Models\Messages\V1\Egress\Zulu === true) {
				$msgObj->setResponseHash($authObj->hash);
				foreach ($dataObj as $key => $val) {
					$msgObj->addRxData($key, $val);
				}
			} else {
				throw new \Exception("Not handled for the type of message provided: '".get_class($msgObj)."'", 1111);
			}

			$this->stdPropsExist($headObj, array("hostTime", "runTime"), true);
			$msgObj->setHostTime($headObj->hostTime);
			$msgObj->setRunTime($headObj->runTime);

			//no meta data on responses

		} catch (\Exception $e) {
			if ($msgObj->getError() === null) {
				//dont override errors, its likely the same issue, as the responding connection discovered
				$msgObj->setError($e);
			}
		}
		$msgObj->setResponseReceived(true);
		
		return $msgObj;
	}
}