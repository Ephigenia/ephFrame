<?php

namespace ephFrame\core;

class Dispatcher
{
	public static function dispatch($url = null)
	{
		$request = new \ephFrame\HTTP\Request();
		$uri = substr($request->path, strlen(Router::base()));
		if ($result = Router::getInstance()->parse($uri)) {
			$controller = new $result['controller']($request);
			$controller->action($result['action'], $result);
			return $controller->__toString();
		}
		return false;
	}
}