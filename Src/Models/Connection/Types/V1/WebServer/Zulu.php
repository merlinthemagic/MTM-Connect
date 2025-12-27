<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\WebServer;

class Zulu extends Process
{
	public function terminate()
	{
		if ($this->isTerm() === false && $this->_initTerm === false) {
			$this->_initTerm	= true;
			
			\MTM\Connect\Facts::getConnections()->unsetCache($this, false);
			
			if ($this->getTermCb() !== null) {
				try {
					call_user_func_array($this->getTermCb(), array($this));
				} catch (\Exception $e) {
					//user issue
				}
			}
			$this->_isTerm		= true;
		}
	}
}
