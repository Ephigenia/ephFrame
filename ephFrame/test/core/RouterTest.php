<?php

namespace ephFrame\test\core;

use ephFrame\HTTP\Request;
use ephFrame\core\Router;
use ephFrame\core\Route;

class RouterTest extends \PHPUnit_Framework_TestCase
{
	public function testGetInstance()
	{
		$this->assertInstanceOf('\ephFrame\core\Router',  Router::getInstance());
	}
	
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
	
	public function testRoutesAdd()
	{
		$Router = new Router();
		$Router->addRoutes(array(
			'namedroute' => new Route('/{:controller}/'),
			new Route('/static/{:page}'),
		));
		$this->assertEquals(
			$Router->namedroute->insert(array('controller' => 'controller')),
			'/controller'
		);
		// check if namedroute gets overwritten
		$Router->addRoutes(array(
			'secondname' => new Route('/'),
			'namedroute' => new Route('/{:action}/'),
		));
		$this->assertInstanceOf('\ephFrame\core\Route', $Router->namedroute);
		$this->assertEquals(
			$Router->namedroute->insert(array('action' => 'newaction')),
			'/newaction'
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