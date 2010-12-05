<?php

namespace ephFrame\core;

class Route
{
	public $template;
	
	public $params = array(
		'controller' => 'app\lib\controller\Controller',
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
		// replace {:var:regex} syntax with regexp catch
		$regexp = preg_replace('@\{:([^\:}]+)(:(.+))\}@', '(?P<\\1>\\3)', $regexp);
		// replace {:var} syntax with regexp catch
		$regexp = preg_replace('@\{:([^\}]+)\}@', '(?P<\\1>[^/]+)', $regexp);
		// add regexp rules for beginning and end of route
		if (strncmp($regexp, '/', 1) == 0) {
			$regexp = '^'.$regexp;
		}
		// replace trailing * with match for all
		if (substr($regexp, -1) == '*') {
			$regexp = substr($regexp, 0, -1).'.*';
		}
		return '@'.$regexp.'$@';
	}
	
	public function insert(Array $array = null)
	{
		$result = $this->template;
		foreach($array + $this->params as $key => $value) {
			$result = preg_replace('@\{:'.preg_quote($key,'@').'\}@', $value, $result);
		}
		// replace remaining placeholders with nothing
		$result = preg_replace('@\{:[^\}]+\}@', '', $result);
		return rtrim($result, '/*');
	}
	
	public function __toString()
	{
		return $this->template;
	}
}