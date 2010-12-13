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
					'controller' => 'user', 'action' => 'index',
				),
				'/user/action' => false,
				'/' => false,
			),
			'/:controller/:action' => array(
				'/user/view' => array(
					'controller' => 'user', 'action' => 'view',
				),
			),
			'/:controller/:id/:action' => array(
				'/user/23/edit' => array(
					'controller' => 'user', 'action' => 'edit', 'id' => '23',
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
		$this->assertEquals($route->parse('/user/ephigenia'), array('username' => 'ephigenia', 'controller' => 'user', 'action' => 'index'));
		$route = new Route('/:controller/:username<\w+>,:id<\d{2,}>.:format');
		$this->assertEquals($route->parse('/user/ephigenia,15.json'), array('username' => 'ephigenia', 'controller' => 'user', 'action' => 'index', 'format' => 'json', 'id' => 15));
	}
	
	public function testInsert()
	{
		$routes = array(
			'/:controller/:id/:action' => array(
				array(
					array('controller' => 'user', 'action' => 'edit', 'id' => 123),
					'/user/123/edit'
				),
				array(
					array('controller' => 'user', 'id' => 123),
					'/user/123/index',
				),
				array(
					array('controller' => 'user'),
					'/user/:id/index',
				),
				array(
					array('controller' => 'user', 'action' => 'view'),
					'/user/:id/view',
				)
			),
			'/:controller' => array(
				array(
					array('controller' => 'user'),
					'/user',
				),
				array(
					array('controller' => 'user', 'action' => 'index'),
					'/user',
				),
			)
		);
		foreach($routes as $route => $tests) {
			$route = new Route($route);
			foreach($tests as $test) {
				$this->assertEquals($route->insert($test[0]), $test[1]);
			}
		}
	}
	
	public function testInsertWithRegexp()
	{
		$route = new Route('/:username<[a+z0-9.-]{3,20}>,:id<\d+>/:action');
		$this->assertEquals(
			$route->insert(array('username' => 'marceleichner', 'id' => 15, 'action' => 'edit')),
			'/marceleichner,15/edit'
		);
	}
	
	public function testAsterisc()
	{
		$route = new Route('/:controller*');
		$this->assertEquals(
			$route->parse('/user/edit'), array('controller' => 'user', 'action' => 'index')
		);
		$route = new Route('/:controller');
		$this->assertFalse($route->parse('/user/edit'));
		$this->assertEquals($route->parse('/user'), array('controller' => 'user', 'action' => 'index'));
	}
	
	public function testRootSlash()
	{
		$route = new Route('/:controller');
		$this->assertEquals(
			$route->parse('/abc'), array('controller' => 'abc', 'action' => 'index')
		);
		$route->template = ':controller/:action';
		$this->assertEquals(
			$route->parse('user/edit'), array('controller' => 'user', 'action' => 'edit')
		);
		$this->assertEquals(
			$route->parse('/abc/def'), array('controller' => 'abc', 'action' => 'def')
		);
	}
	
	public function test__toString()
	{
		$route = new Route('/:controller/:action');
		$this->assertEquals((string) $route, '/:controller/:action');
	}
}