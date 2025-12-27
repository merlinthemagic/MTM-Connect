<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Messages\V1\Egress;

abstract class Alpha extends \MTM\Connect\Models\Messages\V1\Zulu
{
	public function __construct()
	{
		parent::__construct();
		$this->setGuid(\MTM\Utilities\Factories::getGuids()->getV4()->get(false));
		$this->setRsvp(true);
		$this->setExpire(time() + 65); //default timeout
	}
}