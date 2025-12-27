<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1;

abstract class Zulu extends Messages
{
	public function terminate()
	{
		//end all requests that where transmitted over this connection
		foreach ($this->getPendingRequests() as $msgObj) {
			if ($msgObj->getDone() === false) {
				if ($msgObj->getError() === null) {
					$msgObj->setError(new \Exception("Connection was terminated while waiting for RSVP", 1111));
				}
				$msgObj->setDone(true);
			}
		}
	}
}
