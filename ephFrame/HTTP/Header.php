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
		foreach($this->data as $k => $v) {
			if ($k == 'ETag') {
				$v = '"'.$v.'"';
			}
			$lines[] = sprintf('%s: %s', $k, $v);
		}
		return implode($lines, "\r\n");
	}
}