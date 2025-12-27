<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1;

abstract class Routing extends Response
{
	//route
	protected $_rL1="";
	protected $_rL2="";
	protected $_rL3="";
	protected $_rL4="";
	protected $_rL5="";
	protected $_rL6="";
	protected $_rL7="";
	protected $_rL8="";
	protected $_rL9="";
	protected $_rL10="";
	
	public function setL1($val)
	{
		$this->isStrMax($val, 64, true);
		$this->_rL1		= $val;
		return $this;
	}
	public function getL1()
	{
		return $this->_rL1;
	}
	public function setL2($val)
	{
		$this->isStrMax($val, 64, true);
		$this->_rL2		= $val;
		return $this;
	}
	public function getL2()
	{
		return $this->_rL2;
	}
	public function setL3($val)
	{
		$this->isStrMax($val, 64, true);
		$this->_rL3		= $val;
		return $this;
	}
	public function getL3()
	{
		return $this->_rL3;
	}
	public function setL4($val)
	{
		$this->isStrMax($val, 64, true);
		$this->_rL4		= $val;
		return $this;
	}
	public function getL4()
	{
		return $this->_rL4;
	}
	public function setL5($val)
	{
		$this->isStrMax($val, 64, true);
		$this->_rL5		= $val;
		return $this;
	}
	public function getL5()
	{
		return $this->_rL5;
	}
	public function setL6($val)
	{
		$this->isStrMax($val, 64, true);
		$this->_rL6		= $val;
		return $this;
	}
	public function getL6()
	{
		return $this->_rL6;
	}
	public function setL7($val)
	{
		$this->isStrMax($val, 64, true);
		$this->_rL7		= $val;
		return $this;
	}
	public function getL7()
	{
		return $this->_rL7;
	}
	public function setL8($val)
	{
		$this->isStrMax($val, 64, true);
		$this->_rL8		= $val;
		return $this;
	}
	public function getL8()
	{
		return $this->_rL8;
	}
	public function setL9($val)
	{
		$this->isStrMax($val, 64, true);
		$this->_rL9		= $val;
		return $this;
	}
	public function getL9()
	{
		return $this->_rL9;
	}
	public function setL10($val)
	{
		$this->isStrMax($val, 64, true);
		$this->_rL10		= $val;
		return $this;
	}
	public function getL10()
	{
		return $this->_rL10;
	}
}