<?php

namespace ephFrame\HTTP;

class Header
{
	protected $data = array();
	
	public function __construct(Array $data = array())
	{
		$this->data = $data;
	}
	
	public function __get($k)
	{
		return $this->data[$k];
	}
	
	public function __set($k, $v)
	{
		return $this->data[$k] = $v;
	}
	
	public function __toString()
	{
		$lines = array();
		foreach ($this->data as $key => $value) {
			$lines[] = $this->statement($key, $value);
		}
		return implode($lines, "\r\n");
	}
	
	protected function statement($key, $value)
	{
		if (strncasecmp($key, 'etag', 4) == 0) {
			$value = '"'.$value.'"';
		}
		return sprintf('%s: %s', $key, $value);
	}
	
	public function send($overwrite = true)
	{
		foreach($this->data as $k => $v) {
			header($this->statement($k, $v), true);
		}
		return $this;
	}
}