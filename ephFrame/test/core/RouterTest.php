<?php

namespace ephFrame\test\core;

use ephFrame\HTTP\Request;
use ephFrame\core\Router;
use ephFrame\core\Route;

class RouterTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		Router::reset();
	}
	
	public function testConcurrency()
	{
		// add two routes that are almost the same and test which one
		// is parsed
		Router::addRoutes(array(
			new Route('/{:controller}/{:action}'),
			new Route('/{:controller}'),
		));
		$result = Router::parse('/user/edit');
		$this->assertEquals($result, array('controller' => 'user', 'action' => 'edit'));
		$result = Router::parse('/user');
		$this->assertEquals($result, array('controller' => 'user', 'action' => 'index'));
	}
	
	public function testConcurrencyAsterisk()
	{
		Router::addRoutes(array(
			new Route('/{:controller}*'),
			new Route('/{:controller}/{:action}*'),
		));
		$result = Router::parse('/user/edit');
		$this->assertEquals($result, array('controller' => 'user', 'action' => 'index'));
		$result = Router::parse('/user');
		$this->assertEquals($result, array('controller' => 'user', 'action' => 'index'));
	}
}