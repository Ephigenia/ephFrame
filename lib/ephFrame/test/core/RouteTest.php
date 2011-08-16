<?php

namespace ephFrame\test\core;

use ephFrame\core\Route,
	ephFrame\HTTP\Request,
	ephFrame\HTTP\RequestMethod,
	ephFrame\HTTP\Header
	;

/**
 * @group core
 * @group Routing
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$_SERVER['SERVER_NAME'] = 'localhost';
		$_SERVER['REQUEST_URI'] = '/ephFrame/test';
		$this->fixture = new Route('/:controller/:action', array(
			'controller' => 'user',
			'action' => 'index',
		));
	}
	
	public function tearDown()
	{
		unset($_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI']);
	}
	
	public function testUrl()
	{
		$this->assertEquals(substr($this->fixture->url(), 0, 7), 'http://');
	}
	
	public function testAddParamsToQuery()
	{
		$route = new Route('/event/:id/', array(
			'page' => 10,
			'perPage' => 30, // should be ignored when not defined in param array
		));
		$this->assertEquals($route->uri(array('id' => 21, 'page' => 30)), '/event/21?page=30');
	}
	
	public function testUrlHTTPS()
	{
		$_SERVER['HTTPS'] = 'on';
		$this->assertEquals(substr($this->fixture->url(), 0, 8), 'https://');
		unset($_SERVER['HTTPS']);
	}
	
	public function testUri()
	{
		$this->assertEquals($this->fixture->uri(), '/user/index');
	}
	
	public function test__invoke()
	{
		$route = $this->fixture;
		$this->assertEquals($route(array('controller' => 'news')), '/news/index');
	}
	
	public function test__toString()
	{
		$this->assertEquals((string) $this->fixture, '/:controller/:action');
	}
	
	public function testParse()
	{
		$tests = array(
			'/:controller' => array(
				'/user' => array(
					'controller' => 'app\controller\UserController', 'action' => 'index',
				),
				'/user/action' => false,
				'/' => false,
			),
			'/:controller/:action' => array(
				'/user/view' => array(
					'controller' => 'app\controller\UserController', 'action' => 'view',
				),
			),
			'/:controller/:id/:action' => array(
				'/user/23/edit' => array(
					'controller' => 'app\controller\UserController', 'action' => 'edit', 'id' => '23',
				),
				'/user/edit' => false,
				'/user/123' => false,
			),
			'/blog/page-:page<\d+>' => array(
				'/blog/page-666' => array(
					'controller' => 'app\controller\Controller', 'action' => 'index', 'page' => 666,
				),
				'/blog/page-string' => false,
				'/blog/' => false,
			),
		);
		foreach($tests as $route => $test) {
			$route = new Route($route);
			foreach($test as $uri => $expectedResult) {
				$this->assertEquals($route->parse(new Request(RequestMethod::GET, $uri)), $expectedResult);
			}
		}
	}
	
	public function testParseWithRegexpRoute()
	{
		$route = new Route('/:controller/:username<\w+>');
		$this->assertEquals(
			$route->parse(new Request(RequestMethod::GET,'/user/ephigenia')),
			array('username' => 'ephigenia', 'controller' => 'app\controller\UserController', 'action' => 'index')
		);
		$route = new Route('/:controller/:username<\w+>,:id<\d{2,}>.:format');
		$this->assertEquals($route->parse(
			new Request(RequestMethod::GET,'/user/ephigenia,15.json')),
			array('username' => 'ephigenia', 'controller' => 'app\controller\UserController', 'action' => 'index', 'format' => 'json', 'id' => 15)
		);
	}
	
	public function insertEqualValues()
	{
		return array(
			array(
				'/:controller/:id/:action?', 
				array('controller' => 'user', 'action' => 'edit', 'id' => 123),
				'/user/123/edit',
			),
			array(
				'/:controller/:id/:action*', 
				array('controller' => 'user', 'action' => 'edit', 'id' => 123),
				'/user/123/edit',
			),
			array(
				'/:controller/:id/:action?',
				array('controller' => 'user', 'id' => 123),
				'/user/123/index',
			),
			array(
				'/:controller/:id/:action?',
				array('controller' => 'user', 'action' => 'index'),
				'/user/:id/index',
			),
			array(
				'/:controller',
				array('controller' => 'user'),
				'/user',
			),
			array(
				'/:controller',
				array('controller' => 'user', 'action' => 'index'),
				'/user',
			),
		);
	}
	
	/**
	 * @dataProvider insertEqualValues
	 */
	public function testInsert($route, Array $values, $expectedResult)
	{
		$route = new Route($route);
		$this->assertEquals($route->insert($values), $expectedResult);
	}
	
	public function testInsertWithRegexpPlaceholders()
	{
		$route = new Route('/:username<[a+z0-9.-]{3,20}>,:id<\d+>?/:action*');
		$this->assertEquals(
			$route->insert(array('username' => 'marceleichner', 'id' => 15, 'action' => 'edit')),
			'/marceleichner,15/edit'
		);
	}
	
	public function testInsertWithCustomRegexp()
	{
		$route = new Route('/(?P<username>[a+z0-9.-]{3,20}),(?P<id>\d+)?/:action');
		$this->assertEquals(
			$route->insert(array('username' => 'marceleichner', 'id' => 15, 'action' => 'edit')),
			'/marceleichner,15/edit'
		);
	}
	
	public function testAsterisc()
	{
		$route = new Route('/:controller*');
		$this->assertEquals(
			$route->parse(new Request(RequestMethod::GET, '/user/edit')),
			array('controller' => 'app\controller\UserController', 'action' => 'index')
		);
		$route = new Route('/:controller');
		$this->assertFalse($route->parse(new Request(RequestMethod::GET, '/user/edit')));
		$this->assertEquals(
			$route->parse(new Request(RequestMethod::GET, '/user')),
			array('controller' => 'app\controller\UserController', 'action' => 'index')
		);
	}
	
	public function testQuestionMarkNotation()
	{
		$route = new Route('/:controller/?:action?');
		$this->assertEquals(
			$route->parse(new Request(RequestMethod::GET,'/user')),
			array('controller' => 'app\controller\UserController', 'action' => 'index')
		);
		$this->assertEquals(
			$route->parse(new Request(RequestMethod::GET,'/user/')),
			array('controller' => 'app\controller\UserController', 'action' => 'index')
		);
		$this->assertEquals(
			$route->parse(new Request(RequestMethod::GET,'/user/edit')),
			array('controller' => 'app\controller\UserController', 'action' => 'edit')
		);
	}
	
	public function testIdNotation()
	{
		$route = new Route('/:controller/:id/:action');
		$this->assertEquals(
			$route->parse(new Request(RequestMethod::GET,'/user/23/edit')),
			array('controller' => 'app\controller\UserController', 'action' => 'edit', 'id' => '23')
		);
		$this->assertFalse($route->parse(new Request(RequestMethod::GET,'/user/2s3/edit')));
	}
	
	public function testIdFalseNotation()
	{
		$route = new Route('/:controller/:idOfUser/:action');
		$this->assertFalse($route->parse(new Request(RequestMethod::GET,'/user/23/edit')));
	}
	
	public function testRootSlash()
	{
		$route = new Route('/:controller');
		$this->assertEquals(
			$route->parse(new Request(RequestMethod::GET,'/abc')),
			array('controller' => 'app\controller\AbcController', 'action' => 'index')
		);
		$route->template = ':controller/:action';
		$this->assertEquals(
			$route->parse(new Request(RequestMethod::GET,'user/edit')),
			array('controller' => 'app\controller\UserController', 'action' => 'edit')
		);
		$this->assertEquals(
			$route->parse(new Request(RequestMethod::GET,'/abc/def')),
			array('controller' => 'app\controller\AbcController', 'action' => 'def')
		);
	}
	
	public function testRequiredMethods()
	{
		$route = new Route('/:controller/create', array(), array('method' => array(
			RequestMethod::PUT,
		)));
		$this->assertFalse(
			$route->parse(new Request(RequestMethod::GET, '/user/create'))
		);
		$this->assertTrue(
			(bool) $route->parse(new Request(RequestMethod::PUT, '/user/create'))
		);
	}
	
	public function testRequirementSecure()
	{
		$route = new Route('/user/login', array(), array('secure' => true));
		$request = new Request(RequestMethod::GET, '/user/login');
		$this->assertFalse($route->parse($request));
		$request->header->https = true;
		$this->assertTrue((bool) $route->parse($request));
	}
}