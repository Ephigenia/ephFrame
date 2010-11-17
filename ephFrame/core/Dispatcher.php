<?php

namespace ephFrame\core;

class Dispatcher
{
	public function dispatch()
	{
		
	}
	
	public static function run()
	{
		$request = new \ephFrame\HTTP\Request();
		$controller = new \app\lib\controller\Controller($request);
		return $controller->__toString();
	}
}