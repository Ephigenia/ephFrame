<?php

namespace ephFrame\logger\filter;

use
	ephFrame\logger\Event
	;

class Message implements Filter
{
	protected $regexp;
	
	public function __construct($regexp)
	{
		$this->regexp = $regexp;
	}
	
	public function accept(Event $event)
	{
		return preg_match($this->regexp, $event->message) > 0;
	}
}