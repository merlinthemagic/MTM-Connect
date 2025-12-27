<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1;

abstract class Response extends Request
{
	protected $_respRecv=false; //response received status
	protected $_respSent=false; //response sent status
	protected $_hostTime=null; //epoch time on the server that responsed
	protected $_runTime=null; //milisecs it took to complete the request
	
	public function setResponseReceived($val)
	{
		$this->isBoolean($val, true);
		$this->_respRecv	= $val;
		return $this;
	}
	public function getResponseReceived()
	{
		return $this->_respRecv;
	}
	public function setResponseSent($val)
	{
		$this->isBoolean($val, true);
		$this->_respSent	= $val;
		return $this;
	}
	public function getResponseSent()
	{
		return $this->_respSent;
	}
	public function setHostTime($val)
	{
		$this->isEpoch32($val, true);
		$this->_hostTime	= $val;
		return $this;
	}
	public function getHostTime()
	{
		return $this->_hostTime;
	}
	public function setRunTime($val)
	{
		$this->isInt($val, true);
		$this->_runTime	= $val;
		return $this;
	}
	public function getRunTime()
	{
		return $this->_runTime;
	}
}