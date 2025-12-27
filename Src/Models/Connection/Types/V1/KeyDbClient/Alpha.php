<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\KeyDbClient;

abstract class Alpha extends \MTM\Connect\Models\Connection\Types\V1\Zulu
{
	protected $_certObj=null;
	protected $_host=null;
	protected $_port=null;
	protected $_timeout=10.000; //in seconds, float
	protected $_keyDbObj=null;
	
	protected $_username=null; //used for messages
	protected $_password=null; //used for messages
	protected $_pepper=null; //used for messages

	protected $_authUsername=null; //used for authenticating with keyDb
	protected $_authPassword=null; //used for authenticating with keyDb

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
	public function setAuthUsername($val)
	{
		$this->isStrMax($val, 255, false);
		$this->_authUsername	= trim($val);
		return $this;
	}
	public function getAuthUsername()
	{
		return $this->_authUsername;
	}
	public function setAuthPassword($val)
	{
		$this->isStrMax($val, 255, false);
		$this->_authPassword	= $val;
		return $this;
	}
	public function getAuthPassword()
	{
		return $this->_authPassword;
	}
	public function setHost($val)
	{
		$this->isStrMax($val, 255, false);
		$this->_host	= trim($val);
		return $this;
	}
	public function getHost()
	{
		return $this->_host;
	}
	public function setPort($val)
	{
		$this->isUsign32Int($val, true);
		$this->_port	= $val;
		return $this;
	}
	public function getPort()
	{
		return $this->_port;
	}
	public function getTimeout()
	{
		return $this->_timeout;
	}
	public function getKeyDbObj()
	{
		return $this->_keyDbObj;
	}
}