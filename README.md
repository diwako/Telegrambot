# PHP Telegrambot base#
This repo is for a pure php based Telegrambot base

## Requirements ##
* A telegram bot created with Telegrams Botfather
* SSL on your server/webspace
* PHP 5.6

## Purpose ##
While creating my own bot for Telegram I noticed that many PHP based bots need additional plugins or simply do not work on shared webspaces with no root access.
With this all that is requires is SSL and a webspace that has PHP 5.6 support.

## Installation ##
* Drag and drop the Telegrambot.php into a directory of your choosing and include this file into your own php file.

* Supply needed credencials to the base in your php base

* Use the base to your liking (see examples.php as reference)

* set up Telegrams Webhook to point to your php file

## Features ##
This base is able to:

	- get the command from userinput
	- get the parameters from userinput
	- send Messages
	- send Chat Actions
	- send Documents
	- send Photos
	- send Videos
	- send Stickers
	- send Location data
	- get all Sender information

### License ###

  The MIT License (MIT)

Copyright (c) 2015 diwako
