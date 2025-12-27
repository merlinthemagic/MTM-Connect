<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\KeyDbClient;

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
				$msg	= $this->getKeyDbObj()->getSubscriptionMessage();
				if ($msg !== null) {
					
					if ($msg->type === "keyspace-change") {

						$route		= array_values(array_filter(explode("/", $msg->key)));
						$guid		= array_pop($route);
						if ($this->isV4Guid($guid, false) === false) {
							//dont say guid
							throw new \Exception("Key is invalid: '".$msg->key."', must end with instance id", 1111);
						} elseif (count($route) > 10) {
							throw new \Exception("Route is too long: '".$msg->key."'", 1111);
						}
						$msgObj		= \MTM\Connect\Facts::getMessages()->getIngressV1();
						$msgObj->setRxConnGuid($this->getGuid());
						$msgObj->addRxData("action", $msg->msg);
						$msgObj->addRxData("guid", $guid);
						
						$index	= 1;
						foreach ($route as $level) {
							$prop				= "setL".$index;
							$index++;
							$msgObj->$prop($level);
						}

						call_user_func_array($this->getCacheCb(), array($msgObj));
						
					} elseif ($msg->type === "message") {
						$rawMsg		= json_decode(base64_decode($msg->msg));
						$msgObj		= \MTM\Connect\Facts::getUtilities()->getMessagesV1()->handle($this, $rawMsg);
						$msgObj->addMxData("keyDbChannel", $msg->chan)->addMxData("keyDbType", $msg->type);
						
						if ($msgObj->getResponseReceived() === false) {
							if ($this->getRequestCb() !== null) {
								call_user_func_array($this->getRequestCb(), array($msgObj));
							}
						} elseif ($this->getResponseCb() !== null) {
							call_user_func_array($this->getResponseCb(), array($msgObj));
						}
						
					} else {
						throw new \Exception("Not handled for message type: '".$msg->type."'", 1111);
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
			$route		= "";
			if ($msgObj->getL1() != "") {
				$route	.= $msgObj->getL1()."/";
				if ($msgObj->getL2() != "") {
					$route	.= $msgObj->getL2()."/";
					if ($msgObj->getL3() != "") {
						$route	.= $msgObj->getL3()."/";
						if ($msgObj->getL4() != "") {
							$route	.= $msgObj->getL4()."/";
							if ($msgObj->getL5() != "") {
								$route	.= $msgObj->getL5()."/";
								if ($msgObj->getL6() != "") {
									$route	.= $msgObj->getL6()."/";
									if ($msgObj->getL7() != "") {
										$route	.= $msgObj->getL7()."/";
										if ($msgObj->getL8() != "") {
											$route	.= $msgObj->getL8()."/";
											if ($msgObj->getL9() != "") {
												$route	.= $msgObj->getL9()."/";
												if ($msgObj->getL10() != "") {
													$route	.= $msgObj->getL10()."/";
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}

			$this->getKeyDbObj()->getCmdPublish()->setKey($route)->setMessage($rawMsg)->execute();
			
		} else {
			throw new \Exception("Cannot transmit message, this connection is not rx nor tx for the message", 1111);
		}
		return $this;
	}
}