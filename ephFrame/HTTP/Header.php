<?php

namespace ephFrame\HTTP;

class Header extends \ArrayObject
{
	public function __construct(Array $array = array())
	{
		return parent::__construct($array, \ArrayObject::ARRAY_AS_PROPS);
	}
	
	protected function statement($key, $value)
	{
		if (strncasecmp($key, 'etag', 4) == 0) {
			$value = '"'.$value.'"';
		}
		return sprintf('%s: %s', $key, (string) $value);
	}
	
	public function __get($var)
	{
		
	}
	
	public function __toString()
	{
		$lines = array();
		foreach ($this as $key => $value) {
			$lines[] = $this->statement($key, $value);
		}
		return implode($lines, "\r\n");
	}
	
	public function send($overwrite = true)
	{
		foreach($this as $k => $v) {
			header($this->statement($k, $v), true);
		}
		return $this;
	}
}