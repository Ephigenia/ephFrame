<?php

namespace ephFrame\test\data\source\adapter;

use ephFrame\data\source\adapter\SQLite;

class SQLiteTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new SQLite();
		$this->fixture->connect('sqlite:test/fixtures/test.db');
	}
	
	public function testQuery()
	{
		$this->fixture->query('SELECT * FROM posts');
	}
}