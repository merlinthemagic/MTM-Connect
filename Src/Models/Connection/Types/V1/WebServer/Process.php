<?php
//� 2025 Martin Peter Madsen
namespace MTM\Connect\Models\Connection\Types\V1\WebServer;

abstract class Process extends Initialize
{
	public function receiveMessage($evObj)
	{
		if ($this->getRxActive() === true) {
			
			//dont need to run this event any more, there is only a single message ever
			$this->setRxActive(false);
			$evObj->terminate();
			
			try {
				if ($_SERVER["REQUEST_METHOD"] !== "options") {
	
					$method		= strtolower($_SERVER["REQUEST_METHOD"]);
					if ($method === "post") {
						if (array_key_exists("CONTENT_TYPE", $_SERVER) === true) {
							$contentType	= strtolower($_SERVER["CONTENT_TYPE"]);
							if (strpos($contentType, "application/json") !== false) {
								$rawMsg		= file_get_contents("php://input");
								$rawMsg		= json_decode($rawMsg, null, 512, JSON_THROW_ON_ERROR);
								if ($this->stdPropsExist($rawMsg, array("meta"), false) === true) {
									//this is an edge connection, we do not accept meta data from here
									unset($rawMsg->meta);
								}
							} else {
								throw new \Exception("CONTENT_TYPE is not supported", 1400);
							}
						} else {
							throw new \Exception("CONTENT_TYPE is missing", 1400);
						}
						
					} elseif ($method === "get") {
						
						//really mostly for testing as the authentication hash cannot be calculated
						
						if (
							array_key_exists("PHP_AUTH_USER", $_SERVER) === true
							&& array_key_exists("PHP_AUTH_PW", $_SERVER) === true
						) {
							
							$headObj				= new \stdClass();
							$headObj->guid			= \MTM\Utilities\Factories::getGuids()->getV4()->get(false);
							$headObj->rsvp			= true;
							$headObj->rsvpExpire	= time() + 65; //default timeout
							$headObj->type			= "request";
							$headObj->version		= 1;
	
							$authObj				= new \stdClass();
							$authObj->hash			= "00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
							$authObj->id			= $_SERVER["PHP_AUTH_USER"];
							$authObj->key			= $_SERVER["PHP_AUTH_PW"];
							
							$routeObj				= new \stdClass();
							$routeObj->l1			= "";
							$routeObj->l10			= "";
							$routeObj->l2			= "";
							$routeObj->l3			= "";
							$routeObj->l4			= "";
							$routeObj->l5			= "";
							$routeObj->l6			= "";
							$routeObj->l7			= "";
							$routeObj->l8			= "";
							$routeObj->l9			= "";
							
							$dataObj				= new \stdClass();
							
							$rawMsg					= new \stdClass();
							$rawMsg->auth			= $authObj;
							$rawMsg->data			= $dataObj;
							$rawMsg->head			= $headObj;
							$rawMsg->route			= $routeObj;
	
							if (array_key_exists("url", $_GET) === true) {
								$found		= false;
								$index		= 1;
								$parts		= explode("/", $_GET["url"]);
								foreach ($parts as $route) {
									
									if (getenv("app.name") === $route) {
										$found		= true;
									}
									if ($found === true) {
										$prop				= "l".$index;
										$routeObj->$prop	= $route;
										$index++;
										
									} elseif (preg_match("/^(v[0-9])$/", $route) === 1) {
										$found		= true;
									}
								}
							}
	
							$authPepper		= "";
							foreach ($_GET as $attr => $val) {
								if (
									$attr !== "url"
									&& is_string($attr) === true
									&& is_string($val) === true
								) {
									if ($attr === "authPepper") {
										$authPepper		= $val;
									} else {
										$dataObj->$attr	= $val;
									}
								}
							}
	
							$hash						= hash("sha512", json_encode($rawMsg, JSON_UNESCAPED_SLASHES));
							$authObj->hash				= hash("sha512", $hash.$authPepper);
	
						} else {
							//get requests should use basic authentication, its mostly for browser testing
							//use post with credentials to avoid this
							header('WWW-Authenticate: Basic realm="DC-SBP"');
							header("HTTP/1.0 401 Unauthorized");
							header("Content-Type: application/json; charset=utf-8");
							exit();
						}
	
					} else {
						throw new \Exception("Method: '".$method."' is not supported", 1400);
					}
	
					
					$msgObj		= \MTM\Connect\Facts::getUtilities()->getMessagesV1()->handle($this, $rawMsg);
					if ($msgObj->getResponseReceived() === false) {
						if ($this->getRequestCb() !== null) {
							call_user_func_array($this->getRequestCb(), array($msgObj));
							
							if ($msgObj->getRsvp() === false && $msgObj->getDone() === true) {
								//not sure about this placement. or the best method
								//But we want to avoid the http server hanging around when the client is not expecting any rsvp
							
								
								//send response and close the client connection, we still keep going
								//issue is the event loop keeps going after this hanging around until the connection is timed out
// 								ob_start();
// 								header("Content-Type: application/json; charset=utf-8");
// 								header("HTTP/1.0 200 OK");
// 								echo json_encode(new \stdClass(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
// 								header("Content-Length: ".ob_get_length());
// 								header("Connection: close");
// 								ob_end_flush();
// 								ob_flush();
// 								flush();
								
								//signal we received the data and die, faking the response
								header("Content-Type: application/json; charset=utf-8");
								header("HTTP/1.0 200 OK");
								echo json_encode(new \stdClass(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
								exit();
							}
						}
					} elseif ($this->getResponseCb() !== null) {
						call_user_func_array($this->getResponseCb(), array($msgObj));
					}
					
				} else {
					//options preflight request
				}
				
			} catch (\Exception $e) {
				
				$evObj->terminate();
				throw $e;
			}
		}
	}
	public function transmitMessage($msgObj)
	{
		if ($this->isInit() === false) {
			throw new \Exception("Cannot transmit messages, connection is not initialized", 1111);
		} elseif ($msgObj->getRxConnGuid() === $this->getGuid()) {
			//this is a response
			header("Content-Type: application/json; charset=utf-8");
			header("HTTP/1.0 200 OK");
			echo json_encode($msgObj->getResponseMsg(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //dont use pretty print, it adds a ton of white space that e.g. roueros would have to deal with
		} else {
			throw new \Exception("Cannot transmit message, this connection is not rx for the message", 1111);
		}
		return $this;
	}
}