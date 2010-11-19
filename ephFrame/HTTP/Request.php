<?php

namespace ephFrame\HTTP;

use ephFrame\HTTP\Header;
use ephFrame\HTTP\RequestMethod;

class Request
{
	public $method = RequestMethod::GET;

	public $header;

	public $data = array();
	
	public $uri;
	
	public function __construct($method = null, Header $header = null, Array $data = array())
	{
		if (isset($_SERVER['REQUEST_URI'])) {
			$this->uri = $_SERVER['REQUEST_URI'];
		}
		if (isset($_SERVER['REQUEST_METHOD'])) {
			$this->method = $_SERVER['REQUEST_METHOD'];
		}
		if (!empty($data)) {
			$this->data += $data;
		} else {
			if ($this->method == RequestMethod::POST) {
				$this->data = $_POST;
			} else {
				$this->data = $_GET;
			}
		}
		if (!is_null($header)) {
			$this->header = $header;
		} else {
			$client = array();
			foreach($_SERVER as $k => $v) {
				if (strncasecmp($k, 'http_', 5) == 0) $client[substr($k, 5)] = $v;
			}
			$this->header = new Header($client);
		}
	}
}