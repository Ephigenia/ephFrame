<?php

namespace ephFrame\test\core;

use ephFrame\HTTP\Request,
	ephFrame\HTTP\RequestMethod,
	ephFrame\core\Router,
	ephFrame\core\Route
	;

/**
 * @group Routing
 */
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
		$_SERVER['SERVER_NAME'] = 'localhost';
		$_SERVER['REQUEST_URI'] = '/ephFrame/test';
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
	
	public function test__call()
	{
		$this->assertEquals($this->router->scaffold(array('controller' => 'user')), '/user/index');
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function test__callFail()
	{
		$this->assertEquals($this->router->nothing(array('controller' => 'user')), '/user/index');
	}
	
	public function testParseValues()
	{
		return array(
			array(
				new Request(RequestMethod::GET, '/user/edit'), 
				array('controller' => 'app\controller\UserController', 'action' => 'edit')
			),
			array(
				new Request(RequestMethod::GET, '/user/'), 
				array('controller' => 'app\controller\UserController', 'action' => 'index')
			),
		);
	}
	
	/**
	 * @dataProvider testParseValues()
	 */
	public function testParse($request, $expectedResult)
	{
		$this->assertEquals($this->router->parse($request), $expectedResult);
	}
	
	public function testParseFail()
	{
		$this->assertFalse($this->router->parse(new Request(RequestMethod::GET, '/no_matchin/route/23')));
	}
	
	public function testConcurrencyAsterisk()
	{
		$Router = new Router(array(
			new Route('/:controller*'),
			new Route('/:controller/:action'),
		));
		$this->assertEquals(
			$Router->parse(new Request(RequestMethod::GET, '/user/edit')), 
			array('controller' => 'app\controller\UserController', 'action' => 'index')
		);
		$this->assertEquals(
			$Router->parse(new Request(RequestMethod::GET, '/user')),
			array('controller' => 'app\controller\UserController', 'action' => 'index')
		);
	}
}