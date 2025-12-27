<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\WsClient;

abstract class Process extends Initialize
{
	protected $_recvMax=1500; //max poll delay
	protected $_recvMin=1; //minimum poll delay
	protected $_recvIncr=10; //poll delay increment
	protected $_recvCur=10; //current poll delay
	protected $_recvSlow=0; //start slowing down the polling after this time
	protected $_recvGrace=5; //how long do we keep polling fast for new messages after a message 
	
	public function receiveMessage($evObj)
	{
		try {
			
			if ($this->getRxActive() === true) {
				$msg	= $this->getWsSock()->getMessage();
				if ($msg !== null) {
					
					$rawMsg		= json_decode(base64_decode($msg));
					$msgObj		= \MTM\Connect\Facts::getUtilities()->getMessagesV1()->handle($this, $rawMsg);
					if ($msgObj->getResponseReceived() === false) {
						if ($this->getRequestCb() !== null) {
							call_user_func_array($this->getRequestCb(), array($msgObj));
						}
					} elseif ($this->getResponseCb() !== null) {
						call_user_func_array($this->getResponseCb(), array($msgObj));
					}
					
					$this->_recvSlow	= (time() + $this->_recvGrace);
					$this->_recvCur		= $this->_recvMin;
					
				} elseif ($this->_recvSlow < time() && $this->_recvMax > $this->_recvCur) {
					$this->_recvCur		+= $this->_recvIncr;
				}
				
			} else {
				$this->_recvCur		= $this->_recvMax;
			}
			
			$evObj->setNextExecuteDelay($this->_recvCur);
		
		} catch (\Exception $e) {
			$evObj->setNextExecuteDelay($this->_recvCur);
			throw $e;
		}
	}
	public function transmitMessage($msgObj)
	{
		if ($this->isInit() === false) {
			throw new \Exception("Cannot transmit messages, connection is not initialized", 1111);
		} elseif ($msgObj->getTxConnGuid() === $this->getGuid()) {
			$rawMsg		= base64_encode(json_encode($msgObj->getRequestMsg(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
			$this->getWsSock()->sendMessage($rawMsg, "text");
		} else {
			throw new \Exception("Cannot transmit message, this connection is not rx nor tx for the message", 1111);
		}
		return $this;
	}
}