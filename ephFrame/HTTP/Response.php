<?php

namespace ephFrame\HTTP;

use ephFrame\HTTP\Header;
use ephFrame\HTTP\StatusCode;

class Response
{
	public $header;
	
	public $body;
	
	public $status = StatusCode::OK;
	
	public $version = '1.1';
	
	public function __construct($status = null, Header $header = null, $body = null)
	{
		if (!is_null($status)) {
			$this->status = (int) $status;
		}
		if ($header instanceof Header) {
			$this->header = $header;
		} else {
			$this->header = new Header();
		}
		$this->body = (string) $body;
	}
	
	public function __toString()
	{
		return 
			'HTTP 1.1 '.$this->status.' '.StatusCode::message($this->status)."\r\n".
			$this->header."\r\n\r\n".
			$this->body
		;
	}
}