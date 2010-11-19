<?php

namespace ephFrame\core;

use ephFrame\HTTP\Request;

class Router
{
	private static $routes = array();
	
	public static function base()
	{
		$base = '';
		$filename = $_SERVER['PHP_SELF'];
		$uri = $_SERVER['REQUEST_URI'];
		for ($i = strlen($filename); $i >= 0; $i--) {
			if (strncasecmp($filename, $uri, $i) === 0) {
				$base = substr($filename, 0, $i-1);
				break;
			}
		}
		return rtrim($base, '/');
	}

	public static function addRoute(Route $route)
	{
		self::$routes[] = $route;
	}
	
	public static function parse($url)
	{
		foreach(self::$routes as $route) {
			if ($result = $route->parse($url)) return $result;
		}
		return false;
	}
}