<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1;

class Messages extends CallBacks
{
	public function getRequest()
	{
		//we only egress via wsClients right? wrong! push messages goes out via wsServerClients
		$msgObj		= \MTM\Connect\Facts::getMessages()->getEgressV1();
		$msgObj->setTxConnGuid($this->getGuid())->setAuthId($this->getUsername());
		$msgObj->setAuthKey($this->getPassword())->setAuthPepper($this->getPepper());
		return $msgObj;
	}
	public function getPendingRequests()
	{
		//get requests that are currently waiting for RSVP
		return \MTM\Connect\Facts::getUtilities()->getMessagesV1()->getMessagesByConnection($this);
	}
}