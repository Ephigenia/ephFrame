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
	
	public function testRoutesAdd()
	{
		$Router = new Router();
		$Router->addRoutes(array(
			'namedroute' => new Route('/:controller/'),
			new Route('/static/:page'),
		));
		$this->assertEquals($Router->namedroute->template, '/:controller/');
		// check if namedroute gets overwritten
		$Router->addRoutes(array(
			'secondname' => new Route('/'),
			'namedroute' => new Route('/:action'),
		));
		$this->assertEquals($Router->namedroute->template, '/:action');
	}
	
	public function testNamedRouteFind()
	{
		$Router = new Router(array(
			'testRoute' => new Route('/:controller/:action')
		));
		$this->assertEquals($Router['testRoute']->template, '/:controller/:action');
		$this->assertEquals($Router->testRoute->template, '/:controller/:action');
	}
	
	public function testConcurrency()
	{
		$router = new Router(array(
			new Route('/:controller/:action'),
			new Route('/:controller'),
		));
		$this->assertEquals($router->parse('/user/edit'), array('controller' => 'user', 'action' => 'edit'));
		$this->assertEquals($router->parse('/user'), array('controller' => 'user', 'action' => 'index'));
	}
	
	public function testConcurrencyAsterisk()
	{
		$Router = new Router(array(
			new Route('/:controller*'),
			new Route('/:controller/:action'),
		));
		$this->assertEquals(
			$Router->parse('/user/edit'), 
			array('controller' => 'user', 'action' => 'index')
		);
		$this->assertEquals(
			$Router->parse('/user'),
			array('controller' => 'user', 'action' => 'index')
		);
	}
}