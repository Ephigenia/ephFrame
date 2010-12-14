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
	
	public function testBase()
	{
		$_SERVER['REQUEST_URI'] = '';
		$this->assertTrue(is_string(Router::base()));
	}
	
	public function setUp()
	{
		$this->router = new Router(array(
			'scaffold' => new Route('/:controller/:action?'),
			new Route('/static/:page'),
			'home' => new Route('/'),
		));
	}

	public function testRoutesAdd()
	{
		$this->router->addRoutes(array(
			'scaffold' => new Route('/:controller/:action?/'),
		));
		$this->assertEquals($this->router->scaffold->template, '/:controller/:action?/');
	}
	
	public function testNamedRouteFind()
	{
		$this->assertEquals($this->router['scaffold']->template, '/:controller/:action?');
		$this->assertEquals($this->router->scaffold->template, '/:controller/:action?');
	}
	
	public function testParse()
	{
		$this->assertEquals($this->router->parse('/user/edit'), array('controller' => 'app\lib\controller\UserController', 'action' => 'edit'));
		$this->assertEquals($this->router->parse('/user/'), array('controller' => 'app\lib\controller\UserController', 'action' => 'index'));
		$this->assertFalse($this->router->parse('/no_matchin/route/23'));
	}
	
	public function testConcurrencyAsterisk()
	{
		$Router = new Router(array(
			new Route('/:controller*'),
			new Route('/:controller/:action'),
		));
		$this->assertEquals(
			$Router->parse('/user/edit'), 
			array('controller' => 'app\lib\controller\UserController', 'action' => 'index')
		);
		$this->assertEquals(
			$Router->parse('/user'),
			array('controller' => 'app\lib\controller\UserController', 'action' => 'index')
		);
	}
}