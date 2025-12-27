<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect;

class Facts
{
	//USE: $factObj		= \MTM\Connect\Facts::__METHOD__();
	
	protected static $_s=array();
	
	public static function getConnections()
	{
		if (array_key_exists(__FUNCTION__, self::$_s) === false) {
			self::$_s[__FUNCTION__]	=	new \MTM\Connect\Facts\Connections();
		}
		return self::$_s[__FUNCTION__];
	}
	public static function getMessages()
	{
		if (array_key_exists(__FUNCTION__, self::$_s) === false) {
			self::$_s[__FUNCTION__]	=	new \MTM\Connect\Facts\Messages();
		}
		return self::$_s[__FUNCTION__];
	}
	public static function getUtilities()
	{
		if (array_key_exists(__FUNCTION__, self::$_s) === false) {
			self::$_s[__FUNCTION__]	=	new \MTM\Connect\Facts\Utilities();
		}
		return self::$_s[__FUNCTION__];
	}
}