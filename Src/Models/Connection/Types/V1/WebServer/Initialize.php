<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\WebServer;

abstract class Initialize extends Alpha
{
	public function initialize()
	{
		if ($this->isInit() === false) {
			
			try {
				
				$loopObj			= \MTM\Events\Facts::getLoops()->getV1();
				$evObj				= $loopObj->addEvent($this, "receiveMessage");
				
				$errCb				= $this->getErrorCb();
				if ($errCb !== null) {
					$evObj->setErrorCb($errCb[0], $errCb[1]);
				}
				
				$evObj->setNextExecuteDelay(0);
				$this->_isInit		= true;
			
			} catch (\Exception $e) {
				$this->terminate();
				throw $e;
			}
		}
		return $this;
	}
}