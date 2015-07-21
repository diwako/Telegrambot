<?php
include("Telegrambot.php");

$botusername = "";
$token = "";

//update Object received from Telegram server
$response = file_get_contents('php://input');

$bot = new Telegrambot($botusername, $token, $response);

//get the command entered by the user
$command = $bot->getCommand();

//first example
if($command == "/start"){
	//tell the user that the bot is doing something if the operation needs noticeable longer
	//here it is indicating that it is typing
	$bot->sendChatAction("typing");

	//wait for 2 seconds so you can see the effect
	sleep(2);
	
	//send hello world as a reply (second parameter)
	$bot->sendMessage("Hello World",true);
}

//Second example
if($command == "/test"){
	$voteoptions = array(
			array("/vote a", "/vote b"),
			array("/vote c", "/vote d")
			);
	
	$reply_markup = array(
			"keyboard" => $voteoptions, 
			"one_time_keyboard" => true,
			"resize_keyboard" => true);
	
	$bot->sendMessage("Here is an example for custom keyboards", false ,$reply_markup);
}

if($command == "/vote"){
	$params = $bot->getParamsString();
	$reply_markup = array("hide_keyboard" => true, "selective" => true);
	//custom keyboards need to be hidden, otherwise the user can reopen it and vote again
	//selective is set to true so only the user that just voted cannot vote again
	
	$bot->sendMessage($bot->getSenderName() . " voted for option: " . $params, true, $reply_markup);
}

//third example
//user sent something to bot starting with @botusername
if($command == "@" . $botusername){
	if($bot->getParamsString() == "hello"){
		$bot->sendMessage("Hello, " .$bot->getSenderName() . ".");
	}
	else{
		$bot->sendMessage("I didn't quite catch that!");
	}
}

//forth example
if($command == "/file")
{
	$bot->sendDocument("sticker.png");
}

//fith example
if($command == "/photo")
{
	$bot->sendPhoto("sticker.png");
}

//sixth example
if($command == "/sticker")
{
	$bot->sendSticker("sticker.png");
}

//seventh example
if($command == "/location")
{
	$latitude = "40.674568";
	$longitude = "-74.035433";
	$bot->sendLocation($latitude, $longitude);
}

//the telegram server WANTS an okay back so it knows that the message has been processed
//this is to prevent Telegram to resent the update object over and over
return "OK";