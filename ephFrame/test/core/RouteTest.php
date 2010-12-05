<?php

namespace ephFrame\test\core;

use ephFrame\core\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
	public function testAsterisc()
	{
		$route = new Route('/{:controller}*');
		$this->assertEquals(
			$route->parse('/user/edit'), array('controller' => 'user', 'action' => 'index')
		);
		$route = new Route('/{:controller}');
		$this->assertFalse($route->parse('/user/edit'));
		$this->assertEquals($route->parse('/user'), array('controller' => 'user', 'action' => 'index'));
	}
	
	public function testRootSlash()
	{
		$route = new Route('/{:controller}');
		$this->assertEquals(
			$route->parse('/abc'), array('controller' => 'abc', 'action' => 'index')
		);
		$route->template = '{:controller}/{:action}';
		$this->assertEquals(
			$route->parse('abc/def'), array('controller' => 'abc', 'action' => 'def')
		);
		$this->assertEquals(
			$route->parse('/abc/def'), array('controller' => 'abc', 'action' => 'def')
		);
	}
	
	public function testSimpleParse()
	{
		$route = new Route('/{:controller}');
		$this->assertEquals(
			$route->parse('/AppController'), array('controller' => 'AppController', 'action' => 'index')
		);
		$route = new Route('/{:controller}/{:action}');
		$this->assertEquals(
			$route->parse('/AppController/test'), array('controller' => 'AppController', 'action' => 'test')
		);
	}
	
	public function testRegexParse()
	{
		$route = new Route('/{:controller}/{:id:\d+}');
		$this->assertEquals(
			$route->parse('/controller/123'), array('controller' => 'controller', 'action' => 'index', 'id' => '123')
		);
	}
	
	public function testInsert()
	{
		$route = new Route('/{:controller}/{:action}');
		$this->assertEquals(
			$route->insert(array('controller' => 'test', 'action' => 'two')),
			'/test/two'
		);
		$route->template = '/{:controller}/{:page}';
		$this->assertEquals(
			$route->insert(array('controller' => 'test', 'page' => '2')),
			'/test/2'
		);
		$route->template = '/{:controller}/{:page}';
		$this->assertEquals(
			$route->insert(array('controller' => 'test', 'page' => 'my_page is cool!')),
			'/test/my_page is cool!'
		);
		$route->template = '/{:controller}/{:page}';
		$this->assertEquals(
			$route->insert(array('controller' => 'test', 'page' => 'my_page is cool!', 'some_more')),
			'/test/my_page is cool!'
		);
	}
	
	public function testInsertAsterisk()
	{
		$route = new Route('/{:controller}/{:action}*');
		$this->assertEquals(
			$route->insert(array('controller' => 'abc', 'action' => 'def')),
			'/abc/def'
		);
		$route = new Route('/{:controller}/{:action}/*');
		$this->assertEquals(
			$route->insert(array('controller' => 'abc', 'action' => 'def')),
			'/abc/def'
		);
	}
	
	public function testCaseInsensitiveInsert()
	{
		$route = new Route('/{:controller}/{:Action}');
		$this->assertEquals(
			$route->insert(array('controller' => 'test', 'action' => 'two', 'Action' => 'second')),
			'/test/second'
		);
	}
	
	public function testInsertEmptyParam()
	{
		$route = new Route('/{:controller}/{:param1}/{:param2}');
		$this->assertEquals(
			$route->insert(array('controller' => 'test', 'param1' => null)),
			'/test'
		);
	}
}