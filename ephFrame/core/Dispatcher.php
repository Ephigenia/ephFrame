<?php

namespace ephFrame\core;

class Dispatcher
{
	public function dispatch($url)
	{
		
	}
	
	public static function run()
	{
		$controller = new \app\lib\controller\Controller(
			new \ephFrame\HTTP\Request()
		);
		$controller->action('index');
		return $controller->__toString();
	}
}