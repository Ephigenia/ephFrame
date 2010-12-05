<?php

namespace ephFrame\test\core;

use ephFrame\HTTP\Request;
use ephFrame\core\Router;
use ephFrame\core\Route;

class RouterTest extends \PHPUnit_Framework_TestCase
{
	public function testConcurrency()
	{
		// add two routes that are almost the same and test which one
		// is parsed
		$router = new Router();
		$router[] = new Route('/{:controller}/{:action}');
		$router[] = new Route('/{:controller}');
		$result = $router->parse('/user/edit');
		$this->assertEquals($result, array('controller' => 'user', 'action' => 'edit'));
		$result = $router->parse('/user');
		$this->assertEquals($result, array('controller' => 'user', 'action' => 'index'));
	}
	
	public function testConcurrencyAsterisk()
	{
		$Router = new Router();
		$Router[] = new Route('/{:controller}*');
		$Router[] = new Route('/{:controller}/{:action}');
		$this->assertEquals(
			$Router->parse('/user/edit'), 
			array('controller' => 'user', 'action' => 'index')
		);
		$this->assertEquals(
			$Router->parse('/user'),
			array('controller' => 'user', 'action' => 'index')
		);
	}
	
	public function testNamedRouteFind()
	{
		$Router = new Router();
		$Router['testRoute'] = new Route('/{:controller}/{:action}');
		$this->assertEquals(
			$Router['testRoute']->insert(array('controller' => 'controller', 'action' => 'action')),
			'/controller/action'
		);
		$this->assertEquals(
			$Router->testRoute->insert(array('controller' => 'controller', 'action' => 'action')),
			'/controller/action'
		);
	}
}