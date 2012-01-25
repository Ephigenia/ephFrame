<?php

namespace ephFrame\HTTP;

class Request extends Message
{
	public $method = RequestMethod::GET;

	public $data = array();
	
	public $files = array();
	
	public $query = array();
	
	public $path;
	
	public function __construct($method = null, $path = null, Header $header = null, Array $data = array(), Array $files = array())
	{
		$this->path = $path ?: $_SERVER['REQUEST_URI'];
		if ($queryStringStart = strpos($this->path, '?')) {
			$this->path = substr($this->path, 0, $queryStringStart);
		}
		$this->method = $method ?: $_SERVER['REQUEST_METHOD'];
		if ($data) {
			$this->data = $data;
		} else {
			$this->data = $_POST;
			$this->query = $_GET;
		}
		if ($files) {
			$this->files = $files;
		} else {
			$this->files = $_FILES;
		}
		if ($header instanceof Header) {
			$this->header = $header;
		} else {
			$this->header = new Header();
			foreach($_SERVER as $k => $v) {
				if (strncasecmp($k, 'http_', 5) != 0) {
					continue;
				}
				$this->header[substr(strtr(strtolower($k), '_', '-'), 5)] = $v;
			}
		}
	}
	
	public function isMethod($method)
	{
		if (is_array($method)) {
			return in_array($this->method, $method);
		} else {
			return $this->method == $method;
		}
	}
	
	public function isSecure()
	{
		return (
            	(isset($this->header['https']) && (strncmp($this->header['https'], 'on', 2) === 0 || $this->header['https'] == 1))
			||	(isset($this->header['ssl-https']) && (strncmp($this->header['ssl-https'], 'on', 2) == 0 || $this->header['ssl-https'] == 1))
			||	(isset($this->header['x-forwarded-proto']) && strncmp($this->header['x-forwarded-proto'], 'https', 5))
        );
	}
	
	public function isAjax()
    {
        return isset($this->header['x-requested-with']) && $this->header['x-requested-with'] == 'XMLHttpRequest';
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