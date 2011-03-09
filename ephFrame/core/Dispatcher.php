<?php

namespace ephFrame\core;

class Dispatcher
{
	public static function dispatch($url = null)
	{
		$request = new \ephFrame\HTTP\Request();
		$uri = substr($request->path, strlen(Router::base()));
		try {
			if ($result = Router::getInstance()->parse($uri)) {
				$controller = new $result['controller']($request, $result);
				$controller->action($result['action'], $result);
				return $controller->__toString();
			}
		} catch (\Exception $exception) {
			$controller = new \ephFrame\core\ErrorController($request);
			$controller->handleException($exception);
			return $controller->__toString();
		}
		return false;
	}
}