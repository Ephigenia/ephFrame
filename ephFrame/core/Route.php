<?php

namespace ephFrame\core;

class Route
{
	public $template;
	
	public $params = array(
		'controller' => 'Controller',
		'action' => 'index',
	);
	
	public function __construct($template, Array $params = array())
	{
		$this->template = (string) $template;
		$this->params = $params + $this->params;
	}
	
	public function parse($url)
	{
		if (preg_match($this->compile(), (string) $url, $found)) {
			foreach ($found as $index => $match) {
				if (is_int($index)) unset($found[$index]);
			}
			return $found + $this->params;
		}
		return false;
	}
	
	public function compile()
	{
		$regexp = $this->template;
		$regexp = preg_replace('@\{:([^\:}]+)(:(.+))\}@', '(?P<\\1>\\3)', $regexp);
		$regexp = preg_replace('@\{:([^\}]+)\}@', '(?P<\\1>[^/]+)', $regexp);
		if (substr($regexp, -1) == '*') {
			$regexp .= '.+';
		}
		return '@'.$regexp.'$@';
	}
	
	public function insert(Array $array = null)
	{
		$result = $this->template;
		foreach($array + $this->params as $key => $value) {
			$result = preg_replace('@\{:'.preg_quote($key,'@').'\}@', $value, $result);
		}
		return rtrim($result, '/');
	}
}