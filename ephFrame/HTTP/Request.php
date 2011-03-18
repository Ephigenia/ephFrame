<?php

namespace ephFrame\HTTP;

class Request extends Message
{
	public $method = RequestMethod::GET;

	public $data = array();
	
	public $path;
	
	public function __construct($method = null, $path = null, Header $header = null, Array $data = array())
	{
		$this->path = $path ?: $_SERVER['REQUEST_URI'];
		$this->method = $method ?: $_SERVER['REQUEST_METHOD'];
		if ($data) {
			$this->data = $data;
		} else {
			$this->data = array_merge($_GET, $_POST);
		}
		if ($header instanceof Header) {
			$this->header = $header;
		} else {
			$client = array();
			foreach($_SERVER as $k => $v) {
				if (strncasecmp($k, 'http_', 5) == 0) $client[substr(strtr(strtolower($k), '_', '-'), 5)] = $v;
			}
			$this->header = new Header($client);
		}
	}
	
	public function isSecure()
	{
		return (
            	(isset($this->header['https']) && (strncmp($this->header['https'], 'on', 2) == 0 || $this->header['https'] == 1))
			||	(isset($this->header['ssl-https']) && (strncmp($this->header['ssl-https'], 'on', 2) == 0 || $this->header['ssl-https'] == 1))
			||	(isset($this->header['x-forwarded-proto']) && strncmp($this->header['x-forwarded-proto'], 'https', 5))
        );
	}
	
	public function isAjax()
    {
        return isset($this->headers['x-requested-with']) && $this->headers['x-requested-with'] == 'XMLHttpRequest';
    }
	
	public function __toString()
	{
		$query = http_build_query($this->data);
		$path = $this->path;
		if (!empty($query)) {
			if ($this->method == RequestMethod::GET) {
				$path .= '?'.$query;
				$query = '';
			} else {
				$query = "\r\n".$query;
			}
		}
		return trim(
			$this->method.' '.$path.' '.$this->protocol."\r\n".
			(count($this->header) ? "\r\n".$this->header : '').
			$query
		);
	}
}