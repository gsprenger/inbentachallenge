<?php
/**
* Minimalist Curl Wrapper to avoid repetition
**/
class CurlWrapper {

	static function post($url, $headers, $payload) {
		$ch = curl_init();

		curl_setopt_array($ch, array(
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_URL => $url,
	    CURLOPT_HTTPHEADER => $headers,
	    CURLOPT_POST => 1,
	    CURLOPT_POSTFIELDS => json_encode($payload)
		));

		$result = curl_exec($ch);

		curl_close($ch);
		return $result;
	}

	static function get($url, $headers, $payload) {
		$ch = curl_init();

		curl_setopt_array($ch, array(
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_URL => $url,
	    CURLOPT_HTTPHEADER => $headers
		));

		$result = curl_exec($ch);

		curl_close($ch);
		return $result;
	}
}

?>
