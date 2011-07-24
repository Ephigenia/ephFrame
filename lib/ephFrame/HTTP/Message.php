<?php

namespace ephFrame\HTTP;

abstract class Message
{
	public $header;
	
	public $body;
	
	public $protocol = 'HTTP/1.1';
	
	public function __construct($body, Header $header = null)
	{
		$this->header = $header ?: new Header();
		$this->body = (string) $body;
	}
}