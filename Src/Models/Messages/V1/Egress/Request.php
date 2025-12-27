<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1\Egress;

abstract class Request extends Alpha
{
	protected $_authPepper=null; //salt to authenticate the message and calculate the response hash
	
	public function setAuthPepper($val)
	{
		$this->isStrMax($val, 64, true);
		$this->_authPepper	= trim($val);
		return $this;
	}
	public function getAuthPepper()
	{
		return $this->_authPepper;
	}
	public function addData($key=null, $throw=false)
	{
		return $this->addTxData($key, $throw);
	}
	public function getRequestMsg()
	{
		$msgObj						= new \stdClass();
		
		$msgObj->auth				= new \stdClass();
		$msgObj->auth->id			= $this->getAuthId();
		$msgObj->auth->key			= $this->getAuthKey();
		$msgObj->auth->hash			= "00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
		
		$msgObj->head				= new \stdClass();
		$msgObj->head->guid			= $this->getGuid();
		$msgObj->head->type			= "request";
		$msgObj->head->rsvp			= $this->getRsvp();
		$msgObj->head->rsvpExpire	= $this->getExpire();
		$msgObj->head->version		= $this->getVersion();
		
		$msgObj->data				= $this->getTxData();
		
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
		
		$hash						= hash("sha512", json_encode($msgObj, JSON_UNESCAPED_SLASHES));
		$msgObj->auth->hash			= hash("sha512", $hash.$this->getAuthPepper());
		
		//now add meta
		$msgObj->meta				= $this->getMxData();

		return $msgObj;
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
				if ($this->getError() === null) {
					$this->setError($e);
				}
				if ($throw === true) {
					throw $e;
				}
			}
		}
		return $this;
	}
}