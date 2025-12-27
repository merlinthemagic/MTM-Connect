<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Utilities\Messages\V1;

abstract class Requests extends Pending
{
	protected function parseRequest($connObj, $rawMsg)
	{
		if (
			$connObj instanceof \MTM\Connect\Models\Connection\Types\V1\WsServerClient\Zulu === true
			|| $connObj instanceof \MTM\Connect\Models\Connection\Types\V1\WebServer\Zulu === true
		) {
			if ($connObj->isRelay() === false) {
				//e.g. push server where the wsServer is also the worker
				$msgObj		= \MTM\Connect\Facts::getMessages()->getIngressV1();
			} else {
				//edge connection
				$msgObj		= \MTM\Connect\Facts::getMessages()->getRelayV1();
			}
			
		} elseif (
			$connObj instanceof \MTM\Connect\Models\Connection\Types\V1\Queue\Zulu === true
			|| $connObj instanceof \MTM\Connect\Models\Connection\Types\V1\WsClient\Zulu === true
			|| $connObj instanceof \MTM\Connect\Models\Connection\Types\V1\KeyDbClient\Zulu === true
		) {
			$msgObj		= \MTM\Connect\Facts::getMessages()->getIngressV1();
		} else {
			throw new \Exception("Not handled for the type of connection provided: '".get_class($connObj)."'", 1111);
		}
		
		$msgObj->setRxConnGuid($connObj->getGuid());

		try {
			
			$this->stdPropsExist($rawMsg, array("head", "auth", "data", "route"), true);
			
			//headers
			$headObj	= $rawMsg->head;
			$this->stdPropsExist($headObj, array("guid", "type", "rsvp", "rsvpExpire"), true);
			$msgObj->setGuid($headObj->guid);
			$msgObj->setRsvp($headObj->rsvp);
			$msgObj->setExpire($headObj->rsvpExpire);
			
			
			//authentication
			$authObj	= $rawMsg->auth;
			$this->stdPropsExist($authObj, array("id", "key", "hash"), true);
			$msgObj->setAuthId($authObj->id);
			$msgObj->setAuthKey($authObj->key);
			$msgObj->setAuthHash($authObj->hash);
			
			//routing
			$routeObj	= $rawMsg->route;
			$this->stdPropsExist($routeObj, array("l1", "l2", "l3", "l4", "l5", "l6", "l7", "l8", "l9", "l10"), true);
			$msgObj->setL1($routeObj->l1);
			$msgObj->setL2($routeObj->l2);
			$msgObj->setL3($routeObj->l3);
			$msgObj->setL4($routeObj->l4);
			$msgObj->setL5($routeObj->l5);
			$msgObj->setL6($routeObj->l6);
			$msgObj->setL7($routeObj->l7);
			$msgObj->setL8($routeObj->l8);
			$msgObj->setL9($routeObj->l9);
			$msgObj->setL10($routeObj->l10);
			
			$dataObj	= $rawMsg->data;
			$this->isStdClass($dataObj, true);
			foreach ($dataObj as $key => $val) {
				$msgObj->addRxData($key, $val);
			}
			
			if ($this->stdPropsExist($rawMsg, array("meta"), false) === true) {
				//carried forward on requests
				$metaObj	= $rawMsg->meta;
				$this->isStdClass($metaObj, true);
				foreach ($metaObj as $key => $val) {
					$msgObj->addMxData($key, $val);
				}
			}
			
			if ($msgObj->getRsvp() === true) {
				$rsvpMax	= time() + $msgObj->getRsvpMax();
				if ($rsvpMax < $msgObj->getExpire()) {
					$errObj		= new \Exception("Message RSVP expiration is too long, maximum allowed is: '".$msgObj->getRsvpMax()."', but request is for: '".($msgObj->getExpire() - time())."' seconds", 1111);
					$msgObj->setError($errObj);
				}
			}
			
		} catch (\Exception $e) {
			$msgObj->setError($e);
		}
		
		return $msgObj;
	}
}