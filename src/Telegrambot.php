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
	 * Returns the type of message that has been sent to the bot
	 * @return string	Returns type
	 */
	public function getType()
	{
		$keys = array_keys($this->responseData["message"]);
		return $keys[count($keys) - 1];
	}
	
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
	 * Returns the Sticker id from the Message
	 * @return string	Returns the Sticker id
	 */
	public function getStickerId()
	{
		return $this->responseData['message']['sticker']['file_id'];
	}
	
	/**
	 * Returns the Document id from the Message
	 * @return string	Returns the Document id
	 */
	public function getDocumentId()
	{
		return $this->responseData['message']['document']['file_id'];
	}
	
	/**
	 * Returns the Name of the document
	 * @return string	Returns name
	 */
	public function getDocumentName()
	{
		return $this->responseData['message']['document']['file_name'];
	}
	
	/**
	 * Returns the mime_type of the document
	 * @return string	Returns mime_type
	 */
	public function getDocumentType()
	{
		return $this->responseData['message']['document']['mime_type'];
	}
	
	/**
	 * Returns the file_size of the document
	 * @return string	Returns file_size
	 */
	public function getDocumentSize()
	{
		return $this->responseData['message']['document']['file_size'];
	}
	
	/**
	 * Returns the audio id from the Message
	 * @return string	Returns the audio id
	 */
	public function getAudioId()
	{
		return $this->responseData['message']['audio']['file_id'];
	}
	
	/**
	 * Returns the audio duration from the Message
	 * @return string	Returns the audio duration
	 */
	public function getAudioDuration()
	{
		return $this->responseData['message']['audio']['duration'];
	}
	
	/**
	 * Returns the audio mime_type id from the Message
	 * @return string	Returns the audio mime_type
	 */
	public function getAudioType()
	{
		return $this->responseData['message']['audio']['mime_type'];
	}
	
	/**
	 * Returns the video id from the Message
	 * @return string	Returns the video id
	 */
	public function getVideoId()
	{
		return $this->responseData['message']['video']['file_id'];
	}
	
	/**
	 * Returns the video duration from the Message
	 * @return string	Returns the video duration
	 */
	public function getVideoDuration()
	{
		return $this->responseData['message']['video']['duration'];
	}
	
	/**
	 * Returns the video mime_type id from the Message
	 * @return string	Returns the video mime_type
	 */
	public function getVideoType()
	{
		return $this->responseData['message']['video']['mime_type'];
	}
		
	/**
	 * Returns the  photo id from the Message
	 * @return string	Returns the  photo id
	 */
	public function getPhotoId()
	{
		//there are 2 more photo ids for a small and middle one, however using those ids always redirects to the large one
		return $this->responseData['message']['photo'][2][file_id];
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
		$postfields = $this->createPostfields($this->getChatId(),$isreply, $reply_markup);
		$postfields["text"] = $message;
		
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
		$postfields = $this->createPostfields($this->getChatId());
		$postfields["action"] = $type;
		
		$this->makeRequest("sendChatAction", $postfields);
	}
	
	/**
	 * Send a document to the chat
	 * @param string $filestring	file that will up uploaded
	 * @param boolean $isreply	Indicates if the message is a reply from the use sent message
	 * @param array $reply_markup	Options for custom keyboards
	 */
	public function sendDocument($filestring, $isreply = false, $reply_markup = null)
	{
		$this->sendChatAction("upload_document");
		
		//check if the file is local, if the file is not local it will be downloaded into the tmp folder
		$local = $this->setFileLocal($filestring);
		$filestring = $local[0];	//set Filepath
		$online = $local[1];	//bool if file was not local
		
		$headers = ["Content-Type:multipart/form-data"]; // cURL headers for file uploading
		$postfields = $this->createPostfields($this->getChatId(), $isreply, $reply_markup);
		
		if(file_exists($filestring)){
			$postfields["document"] = new CurlFile($filestring, '');
		}
		else{
			$postfields["document"] = $filestring;
		}

		$this->makeRequest("sendDocument", $postfields, $headers);
		
		if($online){
			unlink($filestring);
		}
	}
	
	/**
	 * Send a photo to the chat
	 * @param string $filestring	photo that will up uploaded
	 * @param boolean $isreply	Indicates if the message is a reply from the use sent message
	 * @param array $reply_markup	Options for custom keyboards
	 */
	public function sendPhoto($filestring, $isreply = false, $reply_markup = null)
	{
		$this->sendChatAction("upload_photo");
		
		//check if the file is local, if the file is not local it will be downloaded into the tmp folder
		$local = $this->setFileLocal($filestring);
		$filestring = $local[0];	//set Filepath
		$online = $local[1];	//bool if file was not local
		
		$headers = ["Content-Type:multipart/form-data"]; // cURL headers for file uploading
		$postfields = $this->createPostfields($this->getChatId(), $isreply, $reply_markup);
		
		if(file_exists($filestring)){
			$postfields["photo"] = new CurlFile($filestring, '');
		}
		else{
			$postfields["photo"] = $filestring;
		}

		$this->makeRequest("sendPhoto", $postfields, $headers);
		
		if($online){
			unlink($filestring);
		}
	}
	
	/**
	 * Send a sticker to the chat
	 * @param string $filestring	sticker that will up uploaded
	 * @param boolean $isreply	Indicates if the message is a reply from the use sent message
	 * @param array $reply_markup	Options for custom keyboards
	 */
	public function sendSticker($filestring, $isreply = false, $reply_markup = null)
	{
		$this->sendChatAction("typing");
		
		//check if the file is local, if the file is not local it will be downloaded into the tmp folder
		$local = $this->setFileLocal($filestring);
		$filestring = $local[0];	//set Filepath
		$online = $local[1];	//bool if file was not local
		
		$headers = ["Content-Type:multipart/form-data"]; // cURL headers for file uploading
		$postfields = $this->createPostfields($this->getChatId(), $isreply, $reply_markup);
		
		if(file_exists($filestring)){
			$postfields["sticker"] = new CurlFile($filestring, '');
		}
		else{
			$postfields["sticker"] = $filestring;
		}

		$this->makeRequest("sendSticker", $postfields, $headers);
		
		if($online){
			unlink($filestring);
		}
	}
	
	/**
	 * Send a location to the chat
	 * @param float $lat	latitude
	 * @param float $lon	longitude
	 * @param boolean $isreply	Indicates if the message is a reply from the use sent message
	 * @param array $reply_markup	Options for custom keyboards
	 */
	public function sendLocation($lat, $lon, $isreply = false, $reply_markup = null)
	{		
		$postfields = $this->createPostfields($this->getChatId(), $isreply, $reply_markup);
		$postfields["latitude"] = $lat;
		$postfields["longitude"] = $lon;

		$this->makeRequest("sendLocation", $postfields);
	}
	
	/**
	 * Send audio to the chat
	 * @param string $filestring	audio that will up uploaded
	 * @param boolean $isreply	Indicates if the message is a reply from the use sent message
	 * @param array $reply_markup	Options for custom keyboards
	 */
	public function sendAudio($filestring, $isreply = false, $reply_markup = null)
	{
		$this->sendChatAction("upload_audio");
		
		//check if the file is local, if the file is not local it will be downloaded into the tmp folder
		$local = $this->setFileLocal($filestring);
		$filestring = $local[0];	//set Filepath
		$online = $local[1];	//bool if file was not local
		
		$headers = ["Content-Type:multipart/form-data"]; // cURL headers for file uploading
		$postfields = $this->createPostfields($this->getChatId(), $isreply, $reply_markup);
		
		if(file_exists($filestring)){
			$postfields["audio"] = new CurlFile($filestring, '');
		}
		else{
			$postfields["audio"] = $filestring;
		}

		$this->makeRequest("sendAudio", $postfields, $headers);
		
		if($online){
			unlink($filestring);
		}
	}
	
	/**
	 * Send a video to the chat
	 * @param string $filestring	video that will up uploaded
	 * @param boolean $isreply	Indicates if the message is a reply from the use sent message
	 * @param array $reply_markup	Options for custom keyboards
	 */
	public function sendVideo($filestring, $isreply = false, $reply_markup = null)
	{
		$this->sendChatAction("upload_video");		

		//check if the file is local, if the file is not local it will be downloaded into the tmp folder
		$local = $this->setFileLocal($filestring);
		$filestring = $local[0];	//set Filepath
		$online = $local[1];	//bool if file was not local
		
		$headers = ["Content-Type:multipart/form-data"]; // cURL headers for file uploading
		$postfields = $this->createPostfields($this->getChatId(), $isreply, $reply_markup);
		
		if(file_exists($filestring)){
			$postfields["video"] = new CurlFile($filestring, '');
		}
		else{
			$postfields["video"] = $filestring;
		}

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
	 * @param string $chatId	to which chatId should this message be send
	 * @param boolean $isreply	Indicates if the message is a reply from the use sent message
	 * @param array $reply_markup	Options for custom keyboards
	 * @return array	Array[0] = new/old filepath, Array[1] = was file from somewhere online
	 */
	function createPostfields($chatId, $isreply = false, $reply_markup = null)
	{
		$postfields = ["chat_id" => $chatId];
		
		if($isreply){
			$postfields["reply_to_message_id"] = $this->getMessageId();
		}
		
		if($reply_markup){
			$postfields["reply_markup"] = json_encode($reply_markup);
		}
		
		return $postfields;
	}
	
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