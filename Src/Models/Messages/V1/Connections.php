<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1;

abstract class Connections extends Alpha
{
	protected $_txConn=null; //connection where the request was sent out if this is an egress message
	protected $_rxConn=null; //connection where the request was received if this is an ingress message

	public function setRxConnGuid($val)
	{
		$this->isV4Guid($val, true);
		$this->_rxConn	= $val;
		return $this;
	}
	public function getRxConnGuid()
	{
		return $this->_rxConn;
	}
	public function getRxConn()
	{
		if ($this->_rxConn !== null) {
			return \MTM\Connect\Facts::getConnections()->getByGuid($this->_rxConn, true);
		} else {
			throw new \Exception("Cannot get the RX connection, guid not set", 1111);
		}
	}
	public function setTxConnGuid($val)
	{
		$this->isV4Guid($val, true);
		$this->_txConn	= $val;
		return $this;
	}
	public function getTxConnGuid()
	{
		return $this->_txConn;
	}
	public function getTxConn()
	{
		if ($this->_txConn !== null) {
			return \MTM\Connect\Facts::getConnections()->getByGuid($this->_txConn, true);
		} else {
			throw new \Exception("Cannot get the TX connection, guid not set", 1111);
		}
	}
}