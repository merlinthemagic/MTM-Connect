<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1;

class Alpha extends \MTM\Connect\Models\Connection\Types\Base
{
	protected $_guid=null;
	protected $_isRelay=null; //is this connection used as a relay to e.g. workers?
	protected $_isInit=false;
	protected $_isTerm=false;
	protected $_initTerm=false; //has termination started?
	protected $_rxActive=false;
	
	public function __construct()
	{
		$this->_guid	= \MTM\Utilities\Factories::getGuids()->getV4()->get(false);
	}
	public function getGuid()
	{
		return $this->_guid;
	}
	public function isInit()
	{
		return $this->_isInit;
	}
	public function isTerm()
	{
		return $this->_isTerm;
	}
	public function setRxActive($val)
	{
		$this->isBoolean($val, true);
		if ($val === true) {
			if ($this->getRequestCb() === null) {
				throw new \Exception("Cannot enable RX, connection is missing request call back", 1111);
			} elseif ($this->isInit() === false) {
				throw new \Exception("Cannot enable RX, connection is not initialized", 1111);
			}
		}
		$this->_rxActive	= $val;
		return $this;
	}
	public function getRxActive()
	{
		return $this->_rxActive;
	}
	public function isRelay()
	{
		return $this->_isRelay;
	}
	public function setRelay($val)
	{
		if ($val !== null) {
			$this->isBoolean($val, true);
		}
		$this->_isRelay		= $val;
		return $this;
	}
}