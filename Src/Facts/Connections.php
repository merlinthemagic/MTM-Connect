<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Facts;

class Connections extends Base
{
	protected $_connObjs=array();
	
	public function getWebServerV1()
	{
		$connObj	= new \MTM\Connect\Models\Connection\Types\V1\WebServer\Zulu();
		$this->setCache($connObj);
		return $connObj;
	}
	public function getQueueV1()
	{
		$connObj	= new \MTM\Connect\Models\Connection\Types\V1\Queue\Zulu();
		$this->setCache($connObj);
		return $connObj;
	}
	public function getWsServerV1()
	{
		$connObj	= new \MTM\Connect\Models\Connection\Types\V1\WsServer\Zulu();
		$this->setCache($connObj);
		return $connObj;
	}
	public function getWsClientV1()
	{
		$connObj	= new \MTM\Connect\Models\Connection\Types\V1\WsClient\Zulu();
		$this->setCache($connObj);
		return $connObj;
	}
	public function getKeyDbClientV1()
	{
		$connObj	= new \MTM\Connect\Models\Connection\Types\V1\KeyDbClient\Zulu();
		$this->setCache($connObj);
		return $connObj;
	}
	public function getByGuid($guid, $throw=true)
	{
		$this->isV4Guid($guid, true);
		if (array_key_exists($guid, $this->_connObjs) === true) {
			return $this->_connObjs[$guid];
		} elseif ($throw === true) {
			throw new \Exception("No cached connection with that guid", 88601);
		} else {
			return null;
		}
	}
	public function setCache($connObj, $throw=true)
	{
		if ($connObj instanceof \MTM\Connect\Models\Connection\Types\V1\Alpha === false) {
			throw new \Exception("Invalid input", 1111);
		} elseif (array_key_exists($connObj->getGuid(), $this->_connObjs) === false) {
			$this->_connObjs[$connObj->getGuid()]	= $connObj;
		} elseif ($throw === true) {
			throw new \Exception("Connection already cached", 1111);
		}
	}
	public function unsetCache($connObj, $throw=true)
	{
		if ($connObj instanceof \MTM\Connect\Models\Connection\Types\V1\Alpha === false) {
			throw new \Exception("Invalid input", 1111);
		} elseif (array_key_exists($connObj->getGuid(), $this->_connObjs) === true) {
			unset($this->_connObjs[$connObj->getGuid()]);
		} elseif ($throw === true) {
			throw new \Exception("Connection not cached", 1111);
		}
	}
}