<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1;

abstract class Alpha extends \MTM\Connect\Models\Messages\Base
{
	protected $_guid=null; //message ID
	protected $_initTime=null; //time the message was instanciated
	protected $_version=1; //message version
	protected $_isDone=false;
	
	protected $_errObj=null;

	public function __construct()
	{
		$this->_initTime	= \MTM\Utilities\Factories::getTime()->getMicroEpoch(false);
	}
	public function setGuid($val)
	{
		$this->isV4Guid($val, true);
		$this->_guid	= $val;
		return $this;
	}
	public function getGuid()
	{
		return $this->_guid;
	}
	public function getVersion()
	{
		return $this->_version;
	}
	public function setDone($val)
	{
		$this->isBoolean($val, true);
		$this->_isDone	= $val;
		return $this;
	}
	public function getDone()
	{
		//response received
		return $this->_isDone;
	}
	public function getInitTime()
	{
		return $this->_initTime;
	}
	public function getError()
	{
		return $this->_errObj;
	}
	public function setError($val)
	{
		if ($val !== null) {
			if ($val instanceof \Exception === false) {
				throw new \Exception("Invalid input", 1111);
			}
		}
		$this->_errObj		= $val;
		return $this;
	}
}