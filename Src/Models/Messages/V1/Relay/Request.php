<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1\Relay;

abstract class Request extends Alpha
{
	protected $_reqAuthHash=null; //integrity hash of the message to counter TLS proxies
	
	public function setAuthHash($val)
	{
		$this->isSha512($val, true);
		$this->_reqAuthHash	= $val;
		return $this;
	}
	public function getAuthHash()
	{
		return $this->_reqAuthHash;
	}
	public function sendRequest($throw=true)
	{
		if ($this->getRequestSent() === false) {
			
			try {
				if ($this->getRsvp() === true) {
					\MTM\Connect\Facts::getUtilities()->getMessagesV1()->addToRsvpCache($this);
				}
				$this->getTxConn()->transmitMessage($this);
				$this->setRequestSent(true);
				
				if ($this->getRsvp() === false) {
					$this->setDone(true);
				}

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
	public function getRequestMsg()
	{
		$msgObj						= new \stdClass();
	
		$msgObj->auth				= new \stdClass();
		$msgObj->auth->id			= $this->getAuthId();
		$msgObj->auth->key			= $this->getAuthKey();
		$msgObj->auth->hash			= $this->getAuthHash();
		
		$msgObj->head				= new \stdClass();
		$msgObj->head->guid			= $this->getGuid();
		$msgObj->head->type			= "request";
		$msgObj->head->rsvp			= $this->getRsvp();
		$msgObj->head->rsvpExpire	= $this->getExpire();
		$msgObj->head->version		= $this->getVersion();
		
		$msgObj->data				= $this->getRxData();
		$msgObj->meta				= $this->getMxData();
		
		$msgObj->route				= new \stdClass();
		$msgObj->route->l1			= $this->getL1();
		$msgObj->route->l2			= $this->getL2();
		$msgObj->route->l3			= $this->getL3();
		$msgObj->route->l4			= $this->getL4();
		$msgObj->route->l5			= $this->getL5();
		$msgObj->route->l6			= $this->getL6();
		$msgObj->route->l7			= $this->getL7();
		$msgObj->route->l8			= $this->getL8();
		$msgObj->route->l9			= $this->getL9();
		$msgObj->route->l10			= $this->getL10();
		$msgObj						= $this->orderData($msgObj);

		return $msgObj;
	}
}