<?php

namespace ephFrame\test\core;

use ephFrame\HTTP\Request;
use ephFrame\core\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
	public function testAsterisc()
	{
		$route = new Route('/{:controller}*');
		$this->assertEquals(
			$route->parse('/abc/defg'), array('controller' => 'abc', 'action' => 'index')
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
		$route = new Route('/{:controller}/{:page}/{:page}');
		$this->assertEquals(
			$route->insert(array('controller' => 'test', 'page' => null)),
			'/test'
		);
	}
}