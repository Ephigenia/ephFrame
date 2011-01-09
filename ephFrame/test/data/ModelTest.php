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
	
	public function test__call()
	{
		$this->fixture->findAllById(2);
		$this->fixture->findAllById(2, array('depth' => 2));
	}
	
	public function test__toString()
	{
		$this->fixture->id = 1234;
		$this->fixture->title = 'TestHeadline';
		$this->assertEquals((string) $this->fixture, '1234 TestHeadline');
		$this->fixture->displayField = 'title';
		$this->fixture->title = 'TestHeadline';
		$this->assertEquals((string) $this->fixture, 'TestHeadline');
	}
}