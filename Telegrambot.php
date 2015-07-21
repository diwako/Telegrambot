<?php

class Telegrambot
{
	protected $botusername;
	
	protected $boturl;
	
	protected $responseData = null;
	
	
	/**************************************************
	*					Constructor
	**************************************************/
	
	/**
	 * Telegrambot Class Constructor
	 * @param string $botusername	Bot Username without @
	 * @param string $token	Token returned by the Botfather
	 * @param string $response	Response by the telegram server
	 */
	public function __construct($botusername, $token, $response)
	{	
        $this->botusername = $botusername;
		$this->boturl = "https://api.telegram.org/bot" . $token . "/";
		$this -> responseData = json_decode($response, true);
	}
	
	/**************************************************
	*					Get Functions
	**************************************************/
	
	/**
	 * Returns the name of the Message sender
	 * @return string	Returns the name of the User that just sent a message
	 */
	public function getSenderName()
	{
		return $this->responseData["message"]["from"]["first_name"];
	}
	
	/**
	 * Returns the lastname of the Message sender
	 * @return string	Returns the lastname of the User that just sent a message
	 */
	public function getSenderLastname()
	{
		return $this->responseData["message"]["from"]["last_name"];
	}
	
	/**
	 * Returns the username of the Message sender
	 * @return string	Returns the username of the User that just sent a message
	 */
	public function getSenderUsername()
	{
		return $this->responseData["message"]["from"]["username"];
	}
	
	/**
	 * Returns the username of the Message sender
	 * @return string	Returns the username of the User that just sent a message
	 */
	public function getSenderId()
	{
		return $this->responseData["message"]["from"]["id"];
	}
	
	/**
	 * Returns the Chat ID from where the message was sent from
	 * @return string	Returns the Chat ID
	 */
	public function getChatId()
	{
		return $this->responseData["message"]["chat"]["id"];
	}
	
	/**
	 * Returns the Text from the Message
	 * @return string	Returns the user text
	 */
	public function getText()
	{
		return $this->responseData['message']['text'];
	}
	
	/**
	 * Returns the Id of the Message
	 * @return string	Returns the Id of the Message
	 */
	public function getMessageId()
	{
		return $this->responseData['message']['message_id'];
	}
	
	/**
	 * Returns the command the user has entered
	 * @return string	Returns a command
	 */
	public function getCommand()
	{
		$text = $this -> getText();
		//get Command starting with a / or a @
		if(!($this->startsWith($text, "/") or $this->startsWith($text, "@")))
		{
			return "/error";
		}
		
		if($this->startsWith($text, "@")){
			return "@" . $this->botusername;
		}
		
		//just get the first part to the first space
		$tmp = explode(" ",strtolower($text));
		
		//get rid of @ signs if there are multiple bots in chat
		$tmp = explode("@", $tmp[0]);
		return $tmp[0];
	}
	
	/**
	 * Returns the parameters the user has entered
	 * @return array	Returns parameters
	 */
	public function getParams()
	{
		$text = $this -> getText();
		$tmp = explode(" ",strtolower($text));
		array_shift($tmp);
		return $tmp;
	}
	
	/**
	 * Returns params array as string
	 * @return string	Params array as string
	 */
	public function getParamsString()
	{
		$params = $this->getParams();
		$string = "";
		foreach ($params as $value) {
			$string = $string . " " . $value;
		}
		$string = substr($string, 1);
		return $string;
	}
	
	/**************************************************
	*			Bot to Telegram server functions
	**************************************************/
	
	/**
	 * Send a message back to the chatID that wrote to the bot
	 * @param string $message	Message that will be sent back to the chat
	 * @param boolean $isreply	Indicates if the message is a reply from the use sent message
	 * @param array $reply_markup	Options for custom keyboards
	 */
	public function sendMessage($message, $isreply = false, $reply_markup = null)
	{				
		$postfields = ["text" => $message, "chat_id" => $this->getChatId(), "reply_markup" => json_encode($reply_markup)];
		
		if($isreply){
			$postfields["reply_to_message_id"] = $this->getMessageId();
		}
		
		$this->makeRequest("sendMessage", $postfields);
	}
	
	/**
	 * Send to the chat what the bot is doing at the moment
	 * Used for longer taking operations
	 * $type - what kind of action is the bot doing?
	 *	 typing for text messages, 
	 *	 upload_photo for photos, 
	 *	 record_video or upload_video for videos, 
	 *	 record_audio or upload_audio for audio files, 
	 *	 upload_document for general files, 
	 *	find_location for location data.
	 * @param string $type	type that should be displayed
	 */
	public function sendChatAction($type = "typing")
	{		
		$postfields = ["chat_id" => $this->getChatId(), "action" => $type];
		
		$this->makeRequest("sendChatAction", $postfields);
	}
	
	/**
	 * Send a document to the chat
	 * @param string $filestring	file that will up uploaded
	 */
	public function sendDocument($filestring)
	{
		$this->sendChatAction("upload_document");
		
		//check if the file is local, if the file is not local it will be downloaded into the tmp folder
		$local = $this->setFileLocal($filestring);
		$filestring = $local[0];	//set Filepath
		$online = $local[1];	//bool if file was not local
		
		$headers = ["Content-Type:multipart/form-data"]; // cURL headers for file uploading
		$postfields = ["chat_id" => $this->getChatId(),
			"document" => new CurlFile($filestring, '')];

		$this->makeRequest("sendDocument", $postfields, $headers);
		
		if($online){
			unlink($filestring);
		}
	}
	
	/**
	 * Send a photo to the chat
	 * @param string $filestring	photo that will up uploaded
	 */
	public function sendPhoto($filestring)
	{
		$this->sendChatAction("upload_photo");
		
		//check if the file is local, if the file is not local it will be downloaded into the tmp folder
		$local = $this->setFileLocal($filestring);
		$filestring = $local[0];	//set Filepath
		$online = $local[1];	//bool if file was not local
		
		$headers = ["Content-Type:multipart/form-data"]; // cURL headers for file uploading
		$postfields = ["chat_id" => $this->getChatId(),
			"photo" => new CurlFile($filestring, '')];

		$this->makeRequest("sendPhoto", $postfields, $headers);
		
		if($online){
			unlink($filestring);
		}
	}
	
	/**
	 * Send a sticker to the chat
	 * @param string $filestring	sticker that will up uploaded
	 */
	public function sendSticker($filestring)
	{
		$this->sendChatAction("typing");
		
		//check if the file is local, if the file is not local it will be downloaded into the tmp folder
		$local = $this->setFileLocal($filestring);
		$filestring = $local[0];	//set Filepath
		$online = $local[1];	//bool if file was not local
		
		$headers = ["Content-Type:multipart/form-data"]; // cURL headers for file uploading
		$postfields = ["chat_id" => $this->getChatId(),
			"sticker" => new CurlFile($filestring, '')];

		$this->makeRequest("sendSticker", $postfields, $headers);
		
		if($online){
			unlink($filestring);
		}
	}
	
	/**
	 * Send a location to the chat
	 * @param float $lat	latitude
	 * @param float $lon	longitude
	 */
	public function sendLocation($lat, $lon)
	{		
		$postfields = ["chat_id" => $this->getChatId(),
			"latitude" => $lat,
			"longitude" => $lon];

		$this->makeRequest("sendLocation", $postfields);
	}
	
	/**
	 * Send audio to the chat
	 * @param string $filestring	audio that will up uploaded
	 */
	public function sendAudio($filestring)
	{
		$this->sendChatAction("upload_audio");
		
		//check if the file is local, if the file is not local it will be downloaded into the tmp folder
		$local = $this->setFileLocal($filestring);
		$filestring = $local[0];	//set Filepath
		$online = $local[1];	//bool if file was not local
		
		$headers = ["Content-Type:multipart/form-data"]; // cURL headers for file uploading
		$postfields = ["chat_id" => $this->getChatId(),
			"audio" => new CurlFile($filestring, '')];

		$this->makeRequest("sendAudio", $postfields, $headers);
		
		if($online){
			unlink($filestring);
		}
	}
	
	/**
	 * Send a video to the chat
	 * @param string $filestring	video that will up uploaded
	 */
	public function sendVideo($filestring)
	{
		$this->sendChatAction("upload_video");		

		//check if the file is local, if the file is not local it will be downloaded into the tmp folder
		$local = $this->setFileLocal($filestring);
		$filestring = $local[0];	//set Filepath
		$online = $local[1];	//bool if file was not local
		
		$headers = ["Content-Type:multipart/form-data"]; // cURL headers for file uploading
		$postfields = ["chat_id" => $this->getChatId(),
			"video" => new CurlFile($filestring, '')];

		$this->makeRequest("sendVideo", $postfields, $headers);
		
		if($online){
			unlink($filestring);
		}
	}
	
	/**************************************************
	*				Helper functions
	**************************************************/
	
	/**
	 * checks if a file is not local and downloads that file
	 * @param string $filestring	filepath to either a local or online file
	 * @return array	Array[0] = new/old filepath, Array[1] = was file from somewhere online
	 */
	function setFileLocal($filestring)
	{
		$online = false;
		if(parse_url($filestring, PHP_URL_SCHEME)){
			//file is not local
			$tmp = explode("/", $filestring);
			
			//there is no tmp folder
			if(!file_exists("tmp")){
				mkdir("tmp", 0770);
			}
			
			//download the file into the tmp directory
			file_put_contents("tmp/" . $tmp[count($tmp) - 1], fopen($filestring, 'r'));
			
			$filestring = "tmp/" . $tmp[count($tmp) - 1];
			$online = true;
		}
		return array($filestring, $online);
	}
	
	/**
	 * Sends request back to telegram so a message/file/photo can be sent
	 * @param string $endpoint	Tells function which endpoint for telegram should be reached (Ex. sendMessage)
	 * @param array $postfields	containing the post fields
	 * @param array $headers	containing specific header information
	 */
	function makeRequest($endpoint, $postfields, $headers = null)
	{
		$reply = $this -> boturl . $endpoint;
		
		$ch = curl_init();
		$options = [CURLOPT_URL => $reply,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $postfields,
			CURLOPT_RETURNTRANSFER => 1];
		
		if(isset($headers)){
			$options["CURLOPT_HTTPHEADER"] = $headers;
		}
		curl_setopt_array($ch, $options);
		
		$response = curl_exec($ch);
		
		//debug - Get response from telegram
		// if($endpoint != "sendMessage"){
			// $this->sendMessage($response);
		// }
		curl_close($ch);
	}
	
	/**
	 * Returns if a string starts with a specific substring
	 * @param string $haystack	string to be searched in
	 * @param string $needle	the substring that is being searched for
	 * @return boolean	Returns if substring is the start of string
	 */
	function startsWith($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}

}