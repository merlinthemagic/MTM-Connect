<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Utilities\Messages\V1;

abstract class Alpha extends \MTM\Connect\Utilities\Messages\Base
{
	protected $_evObj=null;
	
	public function __construct()
	{
		$this->_evObj		= \MTM\Events\Facts::getLoops()->getV1()->addEvent($this, "checkMsgs");
		$this->_evObj->setErrorCb($this, "errorCb");
		$this->_evObj->setNextExecuteDelay(0);
	}
}