<?php
require_once __DIR__.'/../CurlWrapper.php';
require_once __DIR__.'/Auth.php';

/**
* Handles API Conversation endpoints
**/
class Conversation {
	const CONVURL = 'https://api-gce3.inbenta.io/prod/chatbot/v1/conversation';

  public static function getSessionToken($accessToken) {
		$result = CurlWrapper::post(
  		self::CONVURL,
  		array(
				'x-inbenta-key: '.Auth::AUTHKEY,
				'Authorization: Bearer '.$accessToken
  		),
  		""
  	);

  	$res = json_decode($result, true);

  	return $res['sessionToken'];
  }

  public static function sendMessage($accessToken, $sessionToken, $message) {
		$result = CurlWrapper::post(
  		self::CONVURL."/message",
  		array(
				'x-inbenta-key: '.Auth::AUTHKEY,
				'Authorization: Bearer '.$accessToken,
				'x-inbenta-session: Bearer '.$sessionToken,
				'Content-Type: application/json'
  		),
  		array(
  			'message' => $message
  		)
  	);

  	$res = json_decode($result, true);

    // If we get a session expired error we need to renew the convo
    if (array_key_exists('errors', $res)) {
      $sessionToken = Conversation::getSessionToken($accessToken);
      $_SESSION['session_token'] = $sessionToken;
      // Recursive call beware
      return Conversation::sendMessage($accessToken, $sessionToken, $message);
    }

  	return array(
  		'message' => $res['answers'][0]['message'],
  		'isNoResult' => in_array('no-results', $res['answers'][0]['flags'], true)
  	);
  }
}

?>
