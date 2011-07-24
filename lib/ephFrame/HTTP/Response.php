<?php

namespace ephFrame\HTTP;

use ephFrame\HTTP\Header;
use ephFrame\HTTP\StatusCode;

class Response extends Message
{
	public $status = StatusCode::OK;
	
	public function __construct($status = null, Header $header = null, $body = null)
	{
		if (!is_null($status)) {
			$this->status = (int) $status;
		}
		return parent::__construct($body, $header);
	}
	
	public function send()
	{
		header($this->protocol.' '.$this->status.' '.StatusCode::message($this->status));
		$this->header->send(true);
		echo $this->body;
		return $this;
	}
	
	public function __toString()
	{
		return trim(
			$this->protocol.' '.$this->status.' '.StatusCode::message($this->status).
			((count($this->header) > 0) ? "\r\n".$this->header : '')
			."\r\n\r\n".
			$this->body
		);
	}
}