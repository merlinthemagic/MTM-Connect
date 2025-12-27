<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1;

class CallBacks extends Alpha
{
	protected $_reqCb=null;
	protected $_respCb=null;
	protected $_errCb=null;
	protected $_termCb=null;
	
	public function setRequestCb($obj, $method)
	{
		if (is_object($obj) === false) {
			throw new \Exception("Invalid input, object expected", 1111);
		} elseif (is_string($method) === false) {
			throw new \Exception("Invalid input, string expected", 1111);
		} elseif (method_exists($obj, $method) === false) {
			throw new \Exception("Invalid input, object does not contain method", 1111);
		}
		$this->_reqCb		= array($obj, $method);
		return $this;
	}
	public function getRequestCb()
	{
		return $this->_reqCb;
	}
	public function setResponseCb($obj, $method)
	{
		if (is_object($obj) === false) {
			throw new \Exception("Invalid input, object expected", 1111);
		} elseif (is_string($method) === false) {
			throw new \Exception("Invalid input, string expected", 1111);
		} elseif (method_exists($obj, $method) === false) {
			throw new \Exception("Invalid input, object does not contain method", 1111);
		}
		$this->_respCb		= array($obj, $method);
		return $this;
	}
	public function getResponseCb()
	{
		return $this->_respCb;
	}
	public function setErrorCb($obj, $method)
	{
		if (is_object($obj) === false) {
			throw new \Exception("Invalid input, object expected", 1111);
		} elseif (is_string($method) === false) {
			throw new \Exception("Invalid input, string expected", 1111);
		} elseif (method_exists($obj, $method) === false) {
			throw new \Exception("Invalid input, object does not contain method", 1111);
		}
		$this->_errCb		= array($obj, $method);
		return $this;
	}
	public function getErrorCb()
	{
		return $this->_errCb;
	}
	public function setTermCb($obj, $method)
	{
		if (is_object($obj) === false) {
			throw new \Exception("Invalid input, object expected", 1111);
		} elseif (is_string($method) === false) {
			throw new \Exception("Invalid input, string expected", 1111);
		} elseif (method_exists($obj, $method) === false) {
			throw new \Exception("Invalid input, object does not contain method", 1111);
		}
		$this->_termCb		= array($obj, $method);
		return $this;
	}
	public function getTermCb()
	{
		return $this->_termCb;
	}
}