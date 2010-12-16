<?php

namespace ephFrame\test\core;

use ephFrame\core\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
	public function testParse()
	{
		$tests = array(
			'/:controller' => array(
				'/user' => array(
					'controller' => 'app\lib\controller\UserController', 'action' => 'index',
				),
				'/user/action' => false,
				'/' => false,
			),
			'/:controller/:action' => array(
				'/user/view' => array(
					'controller' => 'app\lib\controller\UserController', 'action' => 'view',
				),
			),
			'/:controller/:id/:action' => array(
				'/user/23/edit' => array(
					'controller' => 'app\lib\controller\UserController', 'action' => 'edit', 'id' => '23',
				),
				'/user/edit' => false,
				'/user/123' => false,
			),
		);
		foreach($tests as $route => $test) {
			$route = new Route($route);
			foreach($test as $uri => $expectedResult) {
				$this->assertEquals($route->parse($uri), $expectedResult);
			}
		}
	}
	
	public function testParseWithRegexpRoute()
	{
		$route = new Route('/:controller/:username<\w+>');
		$this->assertEquals($route->parse('/user/ephigenia'), array('username' => 'ephigenia', 'controller' => 'app\lib\controller\UserController', 'action' => 'index'));
		$route = new Route('/:controller/:username<\w+>,:id<\d{2,}>.:format');
		$this->assertEquals($route->parse('/user/ephigenia,15.json'), array('username' => 'ephigenia', 'controller' => 'app\lib\controller\UserController', 'action' => 'index', 'format' => 'json', 'id' => 15));
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
			$route->parse('/user/edit'), array('controller' => 'app\lib\controller\UserController', 'action' => 'index')
		);
		$route = new Route('/:controller');
		$this->assertFalse($route->parse('/user/edit'));
		$this->assertEquals($route->parse('/user'), array('controller' => 'app\lib\controller\UserController', 'action' => 'index'));
	}
	
	public function testQuestionMarkNotation()
	{
		$route = new Route('/:controller/?:action?');
		$this->assertEquals($route->parse('/user'), array('controller' => 'app\lib\controller\UserController', 'action' => 'index'));
		$this->assertEquals($route->parse('/user/'), array('controller' => 'app\lib\controller\UserController', 'action' => 'index'));
		$this->assertEquals($route->parse('/user/edit'), array('controller' => 'app\lib\controller\UserController', 'action' => 'edit'));
	}
	
	public function testIdNotation()
	{
		$route = new Route('/:controller/:id/:action');
		$this->assertEquals($route->parse('/user/23/edit'), array('controller' => 'app\lib\controller\UserController', 'action' => 'edit', 'id' => '23'));
		$this->assertFalse($route->parse('/user/2s3/edit'));
	}
	
	public function testIdFalseNotation()
	{
		$route = new Route('/:controller/:idOfUser/:action');
		$this->assertFalse($route->parse('/user/23/edit'));
	}
	
	public function testRootSlash()
	{
		$route = new Route('/:controller');
		$this->assertEquals(
			$route->parse('/abc'), array('controller' => 'app\lib\controller\AbcController', 'action' => 'index')
		);
		$route->template = ':controller/:action';
		$this->assertEquals(
			$route->parse('user/edit'), array('controller' => 'app\lib\controller\UserController', 'action' => 'edit')
		);
		$this->assertEquals(
			$route->parse('/abc/def'), array('controller' => 'app\lib\controller\AbcController', 'action' => 'def')
		);
	}
	
	public function test__toString()
	{
		$route = new Route('/:controller/:action');
		$this->assertEquals((string) $route, '/:controller/:action');
	}
}