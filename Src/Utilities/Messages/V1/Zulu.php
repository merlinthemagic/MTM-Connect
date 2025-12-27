<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Utilities\Messages\V1;

class Zulu extends Responses
{
	public function handle($connObj, $rawMsg)
	{
		try {
			
			if ($connObj instanceof \MTM\Connect\Models\Connection\Types\V1\Zulu === false) {
				throw new \Exception("Invalid connection input", 1111);
			} elseif ($rawMsg instanceof \stdClass === false) {
				throw new \Exception("Invalid message input", 1111);
			}

			$this->stdPropsExist($rawMsg, array("head"), true);
			$this->stdPropsExist($rawMsg->head, array("version", "type"), true);
			$this->isUsign32Int($rawMsg->head->version, true);
			$this->isStrMax($rawMsg->head->type, 32, true);
			
			if ($rawMsg->head->version === 1) {
				if ($rawMsg->head->type === "request") {
					return $this->parseRequest($connObj, $rawMsg);
				} elseif ($rawMsg->head->type === "response") {
					return $this->parseResponse($connObj, $rawMsg);
				} else {
					throw new \Exception("Not handled for type: '".$rawMsg->head->type."'", 1111);
				}
			} else {
				throw new \Exception("Not handled for message version: '".$rawMsg->head->version."'", 1111);
			}
			
		} catch (\Exception $e) {
			switch ($e->getCode()) {
				case 88702: //The message being responded to, does not exist
					throw $e;
				default:
// 					throw $e;
					throw new \Exception("Message is malformed", 1400);
					break;
			}
		}
	}
}