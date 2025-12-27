<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1;

abstract class Data extends Connections
{
	protected $_mxData=null; //data that will be added to a request internally, but is not hashed in authentication as it was not part of the original request
	protected $_txData=null; //data that originates a request and will be sent out a connection
	protected $_rxData=null; //data that is received in response to a request

	public function __construct()
	{
		parent::__construct();
		$this->_txData			= new \stdClass();
		$this->_rxData			= new \stdClass();
		$this->_mxData			= new \stdClass();
	}
	public function addTxData($key, $val)
	{
		$this->isStrMax($key, 64, true);
		$this->_txData->$key	= $val;
		return $this;
	}
	public function getTxData($key=null, $throw=false)
	{
		if ($key === null) {
			return $this->_txData;
		} else {
			$this->isStrMax($key, 64, true);
			if (property_exists($this->_txData, $key) === true) {
				return $this->_txData->$key;
			} elseif ($throw === true) {
				throw new \Exception("TX data key does not exist: " . $key, 1111);
			} else {
				return null;
			}
		}
	}
	public function addRxData($key, $val)
	{
		$this->isStrMax($key, 64, true);
		$this->_rxData->$key	= $val;
		return $this;
	}
	public function getRxData($key=null, $throw=false)
	{
		if ($key === null) {
			return $this->_rxData;
		} else {
			$this->isStrMax($key, 64, true);
			if (property_exists($this->_rxData, $key) === true) {
				return $this->_rxData->$key;
			} elseif ($throw === true) {
				throw new \Exception("RX data key does not exist: " . $key, 1111);
			} else {
				return null;
			}
		}
	}
	public function addMxData($key, $val)
	{
		$this->isStrMax($key, 64, true);
		$this->_mxData->$key	= $val;
		return $this;
	}
	public function getMxData($key=null, $throw=false)
	{
		if ($key === null) {
			return $this->_mxData;
		} else {
			$this->isStrMax($key, 64, true);
			if (property_exists($this->_mxData, $key) === true) {
				return $this->_mxData->$key;
			} elseif ($throw === true) {
				throw new \Exception("MX data key does not exist: " . $key, 1111);
			} else {
				return null;
			}
		}
	}
	protected function orderData($data)
	{
		//the data object will need to be ordered
		//RouterOS can only do alphabetically ordered arrays
		//all data must be ordered alphabetically, so there are no hashing surprises
		//when data is ordered differently
		if ($data instanceof \stdClass === true) {
			$dataArr					= (array) $data;
		} elseif ($this->isArray($data, false) === true) {
			$dataArr					= (array) $data;
		} else {
			throw new \Exception("Invalid input", 1111);
		}

		ksort($dataArr);
		foreach ($dataArr as $i => $dp) {
			if ($this->isArray($dp, false) === true || $this->isStdClass($dp, false) === true) {
				$dataArr[$i]	= $this->orderData($dp);
			} else {
				$dataArr[$i]	= $dp;
			}
		}
		
		if ($data instanceof \stdClass === true) {
			return (object) $dataArr;
		} elseif ($this->isArray($data, false) === true) {
			return $dataArr;
		} else {
			throw new \Exception("Not handled", 1111);
		}
	}
}