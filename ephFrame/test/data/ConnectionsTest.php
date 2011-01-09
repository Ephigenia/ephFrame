<?php

namespace ephFrame\test\data;

use ephFrame\data\Connections;

class ConnectionsTest extends \PHPUnit_Framework_TestCase
{
	public function testAdd()
	{
		Connections::add('test_connections', array(
			'dsn' => 'sqlite:'.__DIR__.'/../fixtures/test.db',
			'adapter' => '\ephFrame\data\source\adapter\MySQL',
		));
		$connection = Connections::get('test_connections');
		$this->assertInstanceOf('\ephFrame\data\source\adapter\MySQL', $connection);
	}
	
	/**
	 * @expectedException ephFrame\data\ConnectionsConnectionNotFoundException
	 */
	public function testGetFailure()
	{
		Connections::get('not_existent');
	}
}
