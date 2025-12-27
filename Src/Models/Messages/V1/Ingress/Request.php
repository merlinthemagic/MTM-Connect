<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1\Ingress;

abstract class Request extends Alpha
{
	protected $_authPepper=null; //salt to authenticate the message and calculate the response hash
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
	public function getData($key=null, $throw=false)
	{
		return $this->getRxData($key, $throw);
	}
	public function authenticateRequest($throw=true)
	{
		//insertion order matters php maintains the order, routeros orders alphabetically
		//if not done alphabetically the hashing will mismatch and fail

		$msgObj						= new \stdClass();
		
		$msgObj->auth				= new \stdClass();
		$msgObj->auth->hash			= "00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
		$msgObj->auth->id			= $this->getAuthId();
		$msgObj->auth->key			= $this->getAuthKey();
		
		$msgObj->data				= $this->getRxData();
		
		$msgObj->head				= new \stdClass();
		$msgObj->head->guid			= $this->getGuid();
		$msgObj->head->rsvp			= $this->getRsvp();
		$msgObj->head->rsvpExpire	= $this->getExpire();
		$msgObj->head->type			= "request";
		$msgObj->head->version		= $this->getVersion();
		
		$msgObj->route				= new \stdClass();
		$msgObj->route->l1			= $this->getL1();
		$msgObj->route->l10			= $this->getL10();
		$msgObj->route->l2			= $this->getL2();
		$msgObj->route->l3			= $this->getL3();
		$msgObj->route->l4			= $this->getL4();
		$msgObj->route->l5			= $this->getL5();
		$msgObj->route->l6			= $this->getL6();
		$msgObj->route->l7			= $this->getL7();
		$msgObj->route->l8			= $this->getL8();
		$msgObj->route->l9			= $this->getL9();
		$msgObj						= $this->orderData($msgObj);
		
		$hash						= hash("sha512", json_encode($msgObj, JSON_UNESCAPED_SLASHES));
		$calcHash					= hash("sha512", $hash.$this->getAuthPepper());
		if ($this->getAuthHash() === $calcHash) {
			return true;
		} elseif ($throw === true) {
			throw new \Exception("Request failed integrity check", 1111);
		} else {
			return false;
		}
	}
}