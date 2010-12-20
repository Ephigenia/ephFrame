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
		$result = false;
		if (preg_match($this->compile(), (string) $url, $found)) {
			foreach ($found as $index => $match) {
				if (is_int($index)) unset($found[$index]);
			}
			$result = $found + $this->params;
			if (!strstr($result['controller'], '\\')) {
				$result['controller'] = 'app\lib\controller\\'.ucFirst($result['controller']).'Controller';
			}
		}
		return $result;
	}
	
	public function compile()
	{
		$regexp = $this->template;
		// :var<regex>
		$regexp = preg_replace('@:([\w]+)(?:<([^>]+)>)@U', '(?P<\\1>\\2)', $regexp);
		// :id
		$regexp = preg_replace('@:id@', '(?P<id>\d+)', $regexp);
		// :var?
		$regexp = preg_replace('@:([\w]+)\?@', '(?P<\\1>[^?\/]+)?', $regexp);
		// :var
		$regexp = preg_replace('@:([\w]+)@', '(?P<\\1>[^?\/]+)', $regexp);
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
			// replace :placeholder and :placeholder<regexp> notations
			$result = preg_replace('@:'.preg_quote($key,'@').'(<[^>]+>)?\??@', $value, $result);
			// replace custom regexps
			$result = preg_replace('@\(\?P<'.preg_quote($key,'@').'>[^)]+\)\??@', $value, $result);
		}
		return rtrim($result, '/*?');
	}
	
	public function url(Array $params = Array())
	{
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			return 'https://'.$_SERVER['SERVER_NAME'].$this->uri($params);
		} else {
			return 'http://'.$_SERVER['SERVER_NAME'].$this->uri($params);
		}
	}
	
	public function uri(Array $params = Array())
	{
		return Router::base().$this->insert($params);
	}
	
	public function __invoke(Array $params = array())
	{
		return $this->uri($params);
	}
	
	public function __toString()
	{
		return $this->template;
	}
}