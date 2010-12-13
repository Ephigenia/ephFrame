<?php

namespace ephFrame\HTTP;

class Request extends Message
{
	public $method = RequestMethod::GET;

	public $data = array();
	
	public $path;
	
	public function __construct($method = null, Header $header = null, Array $data = array())
	{
		if (isset($_SERVER['REQUEST_URI'])) {
			$this->path = $_SERVER['REQUEST_URI'];
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