<?php

namespace ephFrame\test\data;

use ephFrame\data\Model;

class ModelTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Model();
		$this->fixture->connection = 'test';
		$this->fixture->tablename = 'posts';
	}
	
	public function testFind()
	{
		$this->fixture->findAll();
		$this->assertEquals(1,1);
	}
}