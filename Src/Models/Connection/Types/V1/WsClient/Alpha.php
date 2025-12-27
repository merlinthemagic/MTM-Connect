<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\WsClient;

abstract class Alpha extends \MTM\Connect\Models\Connection\Types\V1\Zulu
{
	protected $_certObj=null;
	protected $_host=null;
	protected $_port=null;
	protected $_timeout=30;
	protected $_wsSock=null;
	
	protected $_username=null;
	protected $_password=null;
	protected $_pepper=null;
	
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
	public function setCertificate($val)
	{
		if ($this->isStr($val, false) === true) {
			if (strpos($val, "-----BEGIN CERTIFICATE-----") !== false) {
				//nothing to do already a cert string
			} elseif (strpos($val, DIRECTORY_SEPARATOR) !== false) {
				//likely a file
				$certFile	= \MTM\FS\Factories::getFiles()->getFileFromPath($val);
				$val		= $certFile->getContent();
			}
			$certObj	= \MTM\Certs\Factories::getCerts()->getCRT($val);
			
		} else {
			throw new \Exception("Invalid certificate input", 1111);
		}
		$this->_certObj		= $certObj;
		return $this;
	}
	public function getCertificate()
	{
		return $this->_certObj;
	}
	public function getWsSock()
	{
		return $this->_wsSock;
	}
}