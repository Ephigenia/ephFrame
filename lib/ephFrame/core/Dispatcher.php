<?php

namespace ephFrame\core;

class Dispatcher
{
	public static function dispatch($url = null)
	{
		$request = new \ephFrame\HTTP\Request();
		try {
			if ($result = Router::getInstance()->parse($request)) {
				$controller = new $result['controller']($request, $result);
				$response = $controller->action($result['action'], $result);
				if ($response instanceof \ephFrame\core\Controller) {
					$controller = $response;
				}
				return $controller->getResponse()->send();
			}
		} catch (\Exception $exception) {
			$controller = new \ephFrame\core\ErrorController($request);
			$controller->handleException($exception);
			return $controller->getResponse()->send();
		}
		return false;
	}
}