<?php
include "InbentaAPI/Auth.php";
include "InbentaAPI/Conversation.php";
include "Swapi/Swapi.php";
session_start();

/**
* This class takes care of using the API properly to send a message and stores tokens in the session env to avoid repeating setup calls.
**/
class ChatWithBot {
	function sendMessage($message) {
		// Before all check for occurence of the word "force" since that skips everything else
		if (stripos($message, "force") !== false) {
			$list = Swapi::getFilmList();
			$formattedList = 'The force? Here is a list of all Star Wars movies in order(?): ';
		  foreach ($list as $value) {
    		$formattedList .= htmlspecialchars($value).', ';
  		}
  		$formattedList = rtrim($formattedList, ', ') . '.';
			return $formattedList;
		}

		// First we check if we previously stored authentication data and if it's still valid
		if (!isset($_SESSION['access_token'], $_SESSION['access_token_expiration']) 
			|| time() > $_SESSION['access_token_expiration']) {
			// Our access is no longer valid or never was so let's get a new one
			$result = Auth::getAccessToken();
			$_SESSION['access_token'] = $result['accessToken'];
			$_SESSION['access_token_expiration'] = $result['expiration'];
		}

		// Secondly we check if we have a conversation session token or if we need a new one
		if (!isset($_SESSION['session_token'])) {
			// We need to open a new convo with the bot
			$_SESSION['session_token'] = Conversation::getSessionToken($_SESSION['access_token']);
		}

		// Finally we send our message
		$res = Conversation::sendMessage($_SESSION['access_token'], $_SESSION['session_token'], $message);

		// Check if noresults flag is present (meaning we got a generic answer)
		if ($res['isNoResult']) {
			// Init the value if not present and increment it
			if (!isset($_SESSION['noresult_count'])) {
				$_SESSION['noresult_count'] = 0;
			}
			$_SESSION['noresult_count'] += 1;
		} else {
			// We got a proper answer, reset the counter
			$_SESSION['noresult_count'] = 0;
		}

		// If we got 2 consecutive generic answers, list some SW chars instead
		if ($_SESSION['noresult_count'] >= 2) {
			$_SESSION['noresult_count'] = 0;
			$list = Swapi::getPeopleList();
			$randKeys = array_rand($list, 3);
			$formattedList = 'I am afraid I did not get that question. Here are some random Star Wars characters: '.
				$list[$randKeys[0]].', '.
				$list[$randKeys[1]].', '.
				$list[$randKeys[2]].'.';
			return $formattedList;
		}

		return $res['message'];
	}
}		
?>
