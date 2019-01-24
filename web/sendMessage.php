<?php
include __DIR__.'/../src/ChatWithBot.php';

// Retrieve JSON request body
$toSend = json_decode(file_get_contents('php://input'))->message;

// Send the message over
$bot = new ChatWithBot();
echo $bot->sendMessage($toSend);

?>