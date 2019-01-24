<?php
require_once __DIR__.'/../CurlWrapper.php';

/**
* Handles API Authentication (big surprise)
**/
class Auth {
	const AUTHURL = 'https://api.inbenta.io/v1/auth';
	const AUTHKEY = 'nyUl7wzXoKtgoHnd2fB0uRrAv0dDyLC+b4Y6xngpJDY=';

  public static function getAccessToken() {
		$result = CurlWrapper::post(
  		self::AUTHURL,
  		array(
				'x-inbenta-key: '.self::AUTHKEY,
				'Content-Type: application/json'
  		),
  		array(
  			'secret' => file_get_contents(__DIR__.'/secret')
  		)
  	);

  	$res = json_decode($result, true);

  	return array(
  		'accessToken' => $res['accessToken'],
  		'expiration' => $res['expiration']
  	);
  }
}

?>
