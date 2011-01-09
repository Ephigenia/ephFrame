<?php

namespace ephFrame\test\data\source\adapter;

use ephFrame\data\source\adapter\MySQL;

class MySQLTest extends \PHPUnit_Framework_TestCase
{
	public $db = array(
		'dsn' => 'mysql:host=127.0.0.1;dbname=ditt;charset=utf8',
		'user' => 'root',
		'pass' => false,
	);
	
	public function setUp()
	{
		$this->fixture = new MySQL();
	}
	
	/**
	 * @expectedException \PDOException
	 */
	public function testConnectFail()
	{
		$this->fixture->connect('mysql:host=localhost', 'wronguser', 'pass');
	}
	
	public function testConnect()
	{
		$this->fixture->connect($this->db['dsn'], $this->db['user'], $this->db['pass']);
		$this->assertTrue($this->fixture->isConnected());
	}
	
	public function testDisconnect()
	{
		$this->fixture->connect($this->db['dsn'], $this->db['user'], $this->db['pass']);
		$this->fixture->disconnect();
		$this->assertFalse($this->fixture->isConnected());
	}
	
	public function test__destruct()
	{
		$this->fixture->connect($this->db['dsn'], $this->db['user'], $this->db['pass']);
		unset($this->fixture);
	}
}