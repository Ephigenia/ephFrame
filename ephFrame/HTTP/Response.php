<?php

namespace ephFrame\HTTP;

use ephFrame\HTTP\Header;

class Response
{
	public $header;
	
	public $body;
	
	public $status;
	
	public function __construct($status = null, Header $header = null, $body = null)
	{
		$this->status = (int) $status;
		$this->header = $header;
		$this->body = (string) $body;
	}
	
	public function __toString()
	{
		return $this->body;
	}
}