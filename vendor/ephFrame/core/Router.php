<?php

namespace ephFrame\core;

use ephFrame\HTTP\Request;

class Router extends \ArrayObject
{
	private static $instance;
	
	public $defaults = array(
		
	);
	
	public function __construct(Array $array = array())
	{
		return parent::__construct($array, \ArrayObject::ARRAY_AS_PROPS);
	}
	
	public static function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new Router();
		}
		return self::$instance;
	}
	
	public static function base()
	{
		$base = '';
		$filename = $_SERVER['PHP_SELF'];
		if (!isset($_SERVER['REQUEST_URI'])) {
			return '/';
		}
		$uri = $_SERVER['REQUEST_URI'];
		for ($i = strlen($filename); $i >= 0; $i--) {
			if (strncasecmp($filename, $uri, $i) === 0) {
				$base = substr($filename, 0, $i-1);
				break;
			}
		}
		return rtrim($base, '/');
	}
	
	public function addRoutes(Array $routes)
	{
		$this->exchangeArray($routes + (array) $this);
		return $this;
	}
	
	public function parse(\ephFrame\HTTP\Request $request)
	{
		foreach($this as $route) {
			if ($result = $route->parse($request)) return $result;
		}
		return false;
	}
	
	public function __call($name, $args)
	{
		return $this[$name]($args[0] + $this->defaults);
	}
}