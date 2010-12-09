<?php

namespace ephFrame\core;

class Dispatcher
{
	public static function dispatch($url = null)
	{
		$request = new \ephFrame\HTTP\Request();
		$uri = substr($request->uri, strlen(Router::base()));
		if ($result = Router::getInstance()->parse($uri)) {
			// $result['controller'] = 'app\lib\controller\\'.$result['controller'].'Controller';
			die(var_dump($result));
			$controller = new $result['controller']($request);
			$controller->action($result['action'], $result);
			return $controller->__toString();
		}
		return false;
	}
}