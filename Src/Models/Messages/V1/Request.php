<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1;

abstract class Request extends Data
{
	protected $_rsvp=false; //does the message require a reply
	protected $_rsvpMax=300; //max time a request is allowed to be waiting for rsvp
	protected $_rsvpGrace=5; //max time left on the rsvp or we will not process and return error instead. Requests should go up to the limit
	protected $_reqExpire=null; //when must the reply be received by
	
	protected $_reqAuthId=null; //guid for the cnc session
	protected $_reqAuthKey=null; //key for the session

	protected $_reqSent=false; //request sent status
	
	public function setRsvp($val)
	{
		$this->isBoolean($val, true);
		$this->_rsvp	= $val;
		return $this;
	}
	public function getRsvp()
	{
		return $this->_rsvp;
	}
	public function setExpire($val)
	{
		$this->isEpoch32($val, true);
		$this->_reqExpire	= $val;
		return $this;
	}
	public function getExpire()
	{
		return $this->_reqExpire;
	}
	public function setRsvpMax($val)
	{
		$this->isUsign32Int($val, true);
		$this->_rsvpMax	= $val;
		return $this;
	}
	public function getRsvpMax()
	{
		return $this->_rsvpMax;
	}
	public function setRsvpGrace($val)
	{
		$this->isUsign32Int($val, true);
		$this->_rsvpGrace	= $val;
		return $this;
	}
	public function getRsvpGrace()
	{
		return $this->_rsvpGrace;
	}
	public function setAuthId($val)
	{
		$this->isV4Guid($val, true);
		$this->_reqAuthId	= $val;
		return $this;
	}
	public function getAuthId()
	{
		return $this->_reqAuthId;
	}
	public function setAuthKey($val)
	{
		$this->isSha256($val, true);
		$this->_reqAuthKey	= $val;
		return $this;
	}
	public function getAuthKey()
	{
		return $this->_reqAuthKey;
	}
	public function setRequestSent($val)
	{
		$this->isBoolean($val, true);
		$this->_reqSent	= $val;
		return $this;
	}
	public function getRequestSent()
	{
		return $this->_reqSent;
	}
}