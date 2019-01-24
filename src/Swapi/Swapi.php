<?php
require_once __DIR__.'/../CurlWrapper.php';

/**
* Handles Swapi API requests and answer formatting
**/
class Swapi {
	const APIURL = 'https://swapi.co/api/';

  public static function getFilmList() {
		$result = CurlWrapper::get(
  		self::APIURL.'films/',
  		array(),
  		array()
  	);

  	$res = json_decode($result, true);
  	return array_column($res['results'], 'title');
  }

  public static function getPeopleList() {
		$result = CurlWrapper::get(
  		self::APIURL.'people/',
  		array(),
  		array()
  	);
  	
  	$res = json_decode($result, true);
  	return array_column($res['results'], 'name');
  }
}

?>
