<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\WsServer;

abstract class Alpha extends \MTM\Connect\Models\Connection\Types\V1\Zulu
{
	protected $_certObj=null;
	protected $_host=null;
	protected $_port=null;
	protected $_wsObj=null;
	
	public function setCertificate($cert, $key)
	{
		if ($this->isStr($cert, false) === true) {
			if (strpos($cert, "-----BEGIN CERTIFICATE-----") !== false) {
				//nothing to do already a cert string
			} elseif (strpos($cert, DIRECTORY_SEPARATOR) !== false) {
				//likely a file
				$certFile	= \MTM\FS\Factories::getFiles()->getFileFromPath($cert);
				$cert		= $certFile->getContent();
			}
			$certObj	= \MTM\Certs\Factories::getCerts()->getCRT($cert);
			
		} else {
			throw new \Exception("Invalid certificate input", 1111);
		}
		if ($this->isStr($key, false) === true) {
			if (strpos($key, "-----BEGIN PRIVATE KEY-----") !== false) {
				//nothing to do already a key string
			} elseif (strpos($key, DIRECTORY_SEPARATOR) !== false) {
				//likely a file
				$keyFile	= \MTM\FS\Factories::getFiles()->getFileFromPath($key);
				$key		= $keyFile->getContent();
			}
			$keyObj	= \MTM\Encrypt\Factories::getRSA()->getPrivateKey($key);
			
		} else {
			throw new \Exception("Invalid key input", 1111);
		}
		$certObj->setPrivateKey($keyObj);
		$this->_certObj		= $certObj;
		return $this;
	}
	public function getCertificate()
	{
		return $this->_certObj;
	}
	public function setBinding($host, $port)
	{
		$this->isStrMax($host, 255, false);
		$this->isUsign32Int($port, true);
		$this->_host	= trim($host);
		$this->_port	= $port;
		return $this;
	}
	public function getHost()
	{
		return $this->_host;
	}
	public function getPort()
	{
		return $this->_port;
	}
	public function getWsObj()
	{
		return $this->_wsObj;
	}
}