<?php

namespace ephFrame\core;

class Route
{
	public $template;
	
	public $params = array(
		'controller' => 'Controller',
		'action' => 'index',
		'namespace' => 'app\controller',
	);
	
	public $secure = false;
	
	public $method = array(
		\ephFrame\HTTP\RequestMethod::POST, 
		\ephFrame\HTTP\RequestMethod::GET,
	);
	
	public function __construct($template, Array $params = array(), Array $requirements = array())
	{
		$this->template = (string) $template;
		$this->params = $params + $this->params;
		foreach($requirements as $k => $v) {
			$this->{$k} = $v;
		}
	}
	
	public function parse($request)
	{
		if ($this->method && !$request->isMethod($this->method)) {
			return false;
		}
		if ($this->secure && !$request->isSecure()) {
			return false;
		}
		// parse url stuff
		$uri = substr($request->path, strlen(Router::base()));
		if (!preg_match($this->compile(), (string) $uri, $found)) {
			return false;
		}
		foreach ($found as $index => $match) {
			if (is_int($index)) unset($found[$index]);
		}
		$result = $found + $this->params;
		if (!strstr($result['controller'], '\\')) {
			if ($result['controller'] !== 'Controller') {
				$append = 'Controller';
			} else {
				$append = '';
			}
			$result['controller'] = trim($result['namespace'], '\\').'\\'.ucFirst($result['controller']).$append;
		}
		unset($result['namespace']);
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
			// cut 'controoler' and namespace from controller
			if ($key == 'controller') {
				$value = substr(strrchr('\\'.$value, '\\'), 1, strlen('Controller')-1);
			}
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