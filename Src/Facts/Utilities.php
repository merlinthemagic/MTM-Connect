<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Facts;

class Utilities extends Base
{
	public function getMessagesV1()
	{
		if (array_key_exists(__FUNCTION__, $this->_s) === false) {
			$this->_s[__FUNCTION__]		= new \MTM\Connect\Utilities\Messages\V1\Zulu();
		}
		return $this->_s[__FUNCTION__];
	}
}