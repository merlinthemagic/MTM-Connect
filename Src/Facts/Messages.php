<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Facts;

class Messages extends Base
{
	protected $_rsvpMax=300; //max time a request is allowed to be waiting for rsvp
	
	//max time left on the rsvp or we will not process and return error instead. Requests should have a smaller grace
	//so relays can trigger a bit before. That means a request gets a response rather than a timeout locally
	//+ a late message response that needs to be discarded
	protected $_reqGrace=2;
	protected $_relayGrace=5;
	
	public function getRelayV1()
	{
		$msgObj			= new \MTM\Connect\Models\Messages\V1\Relay\Zulu();
		$msgObj->setRsvpMax($this->_rsvpMax)->setRsvpGrace($this->_relayGrace);
		return $msgObj;
	}
	public function getIngressV1()
	{
		$msgObj			= new \MTM\Connect\Models\Messages\V1\Ingress\Zulu();
		$msgObj->setRsvpMax($this->_rsvpMax)->setRsvpGrace($this->_reqGrace);
		return $msgObj;
	}
	public function getEgressV1()
	{
		$msgObj			= new \MTM\Connect\Models\Messages\V1\Egress\Zulu();
		$msgObj->setRsvpMax($this->_rsvpMax)->setRsvpGrace($this->_reqGrace);
		return $msgObj;
	}
}