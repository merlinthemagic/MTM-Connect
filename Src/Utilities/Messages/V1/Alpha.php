<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Utilities\Messages\V1;

abstract class Alpha extends \MTM\Connect\Utilities\Messages\Base
{
	protected $_evObj=null;
	protected $_msgObjs=array();
	
	public function __construct()
	{
		$this->_evObj		= \MTM\Events\Facts::getLoops()->getV1()->addEvent($this, "checkMsgs");
		$this->_evObj->setErrorCb($this, "errorCb");
		$this->_evObj->setNextExecuteDelay(0);
	}
	public function getMessages()
	{
		return $this->_msgObjs;
	}
	public function addToRsvpCache($msgObj, $throw=true)
	{
		if ($msgObj instanceof \MTM\Connect\Models\Messages\V1\Zulu === false) {
			throw new \Exception("invalid input", 1111);
		} elseif (array_key_exists($msgObj->getGuid(), $this->_msgObjs) === false) {
			$this->_msgObjs[$msgObj->getGuid()]		= $msgObj;
			$this->_evObj->setNextExecuteDelay(0);
		} elseif ($throw === true) {
			throw new \Exception("Message already in cache", 1111);
		}
		return $this;
	}
	public function shiftFromRsvpCacheByGuid($guid, $throw=true)
	{
		$this->isV4Guid($guid, true);
		if (array_key_exists($guid, $this->_msgObjs) === true) {
			$msgObj		= $this->_msgObjs[$guid];
			unset($this->_msgObjs[$guid]);
			return $msgObj;
		} elseif ($throw === true) {
			throw new \Exception("No cached message with that guid", 88701);
		} else {
			return null;
		}
	}
	public function checkMsgs($evObj)
	{
		$nTime		= 2147483647;
		foreach ($this->_msgObjs as $index => $msgObj) {
			if ($nTime > $msgObj->getExpire() && $msgObj->getExpire() >= time()) {
				$nTime	= $msgObj->getExpire();
			}
			
			if (($msgObj->getExpire() - $msgObj->getRsvpGrace()) < time()) {
				if ($msgObj->getDone() === false) {
					unset($this->_msgObjs[$index]);
					
					$msgObj->setError(new \Exception("Failed to receive a response in time", 1111));
					if ($msgObj->getRequestSent() === true) {
						try {
							//should we send these to error CB instead?
							call_user_func_array($msgObj->getRxConn()->getResponseCb(), array($msgObj));
							$msgObj->setDone(true);
						} catch (\Exception $e) {
							$msgObj->setDone(true);
						}
					}
				}
			}
		}
		if ($nTime < 2147483647) {
			$evObj->setNextExecute(($nTime - 1).".000001");
		}
	}
	public function errorCb($obj, $e)
	{
		$rData		= array();
		$rData[]	= "Exception";
		$rData[]	= $e->getMessage();
		$rData[]	= $e->getCode();
		$rData[]	= $e->getTraceAsString();
		
		echo "\n <code><pre> \nClass:  ".__CLASS__." \nMethod:  ".__FUNCTION__. "  \n";
		// 		var_dump($sessObj->replaceKey());
		echo "\n 2222 \n";
		//print_r($_GET);
		echo "\n 3333 \n";
		print_r($rData);
		echo "\n ".time()."</pre></code> \n ";
		die("end");
	}
}