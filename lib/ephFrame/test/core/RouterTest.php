<?php

namespace ephFrame\test\core;

use
	ephFrame\HTTP\Request,
	ephFrame\HTTP\RequestMethod,
	ephFrame\HTTP\Header,
	ephFrame\core\Router,
	ephFrame\core\Route
	;

/**
 * @group core
 * @group Routing
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
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
	
	public function testGetInstance()
	{
		$this->assertInstanceOf('\ephFrame\core\Router', Router::getInstance());
	}
	
	public function testBase()
	{
		$_SERVER['REQUEST_URI'] = '';
		$this->assertTrue(is_string(Router::base()));
		unset($_SERVER['REQUEST_URI']);
		$this->assertEquals('/', Router::base());
	}

	public function testRoutesAdd()
	{
		$this->router->addRoutes(array(
			'scaffold' => new Route('/:controller/:action?/'),
		));
		$this->assertEquals('/:controller/:action?/', $this->router->scaffold->template);
	}
	
	public function testNamedRouteFind()
	{
		$this->assertEquals('/:controller/:action?', $this->router['scaffold']->template);
		$this->assertEquals('/:controller/:action?', $this->router->scaffold->template);
	}
	
	public function test__call()
	{
		$this->assertEquals(
			'/user/index',
			$this->router->scaffold(array('controller' => 'user'))
		);
		$this->assertEquals(
			'',
			$this->router->home()
		);
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function test__callFail()
	{
		$this->assertEquals(
			'/user/index',
			$this->router->nothing(array('controller' => 'user'))
		);
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
		$this->assertEquals($expectedResult, $this->router->parse($request));
	}
	
	public function testParseFail()
	{
		$this->assertFalse($this->router->parse(new Request(RequestMethod::GET, '/no_matchin/route/23')));
	}
	
	public function testRequiredMethod()
	{
		$Router = new Router(array(
			new Route('/user', array(), array('method' => \ephFrame\HTTP\RequestMethod::POST)),
		));
		$this->assertFalse($Router->parse(new Request(RequestMethod::GET, '/user')));
		$this->assertTrue((bool) $Router->parse(new Request(RequestMethod::POST, '/user')));
	}
	
	public function testRequiredSecure()
	{
		$Router = new Router(array(
			new Route('/user', array(), array('secure' => true)),
		));
		$SSLRequest = new Request(RequestMethod::GET, '/user', new Header(array('https' => true)));
		$NormalRequest = new Request(RequestMethod::GET, '/user');
		$this->assertTrue((bool) $Router->parse($SSLRequest));
		$this->assertFalse((bool) $Router->parse($NormalRequest));
	}
	
	public function testConcurrencyAsterisk()
	{
		$Router = new Router(array(
			new Route('/:controller*'),
			new Route('/:controller/:action'),
		));
		$this->assertEquals(
			array('controller' => 'app\controller\UserController', 'action' => 'index'),
			$Router->parse(new Request(RequestMethod::GET, '/user/edit'))
		);
		$this->assertEquals(
			array('controller' => 'app\controller\UserController', 'action' => 'index'),
			$Router->parse(new Request(RequestMethod::GET, '/user'))
		);
	}
}