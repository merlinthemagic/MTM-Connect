<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\WsServerClient;

abstract class Alpha extends \MTM\Connect\Models\Connection\Types\V1\Zulu
{
	protected $_wsSock=null;
	protected $_wsServer=null;
	
	//auth is needed when server clients push messages
	protected $_username=null;
	protected $_password=null;
	protected $_pepper=null;
	
	public function setWsSock($val)
	{
		if ($val instanceof \MTM\WsSocket\Models\Client\V1\Session\Zulu === false) {
			throw new \Exception("Invalid input", 1111);
		}
		$this->_wsSock	= $val;
		$val->setTermCb($this, "socketTermCb");
		return $this;
	}
	public function getWsSock()
	{
		return $this->_wsSock;
	}
	public function setWsServer($val)
	{
		if ($val instanceof \MTM\Connect\Models\Connection\Types\V1\WsServer\Zulu === false) {
			throw new \Exception("Invalid input", 1111);
		}
		$this->_wsServer	= $val;
		return $this;
	}
	public function getWsServer()
	{
		return $this->_wsServer;
	}
	public function setUsername($val)
	{
		$this->isStrMax($val, 255, false);
		$this->_username	= trim($val);
		return $this;
	}
	public function getUsername()
	{
		return $this->_username;
	}
	public function setPassword($val)
	{
		$this->isStrMax($val, 255, false);
		$this->_password	= $val;
		return $this;
	}
	public function getPassword()
	{
		return $this->_password;
	}
	public function setPepper($val)
	{
		$this->isStrMax($val, 255, false);
		$this->_pepper	= $val;
		return $this;
	}
	public function getPepper()
	{
		return $this->_pepper;
	}
}