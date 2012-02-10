<?php

namespace ephFrame\test\validator;

use ephFrame\validator\MinLength;

/**
 * @group Validator
 */
class MinLengthTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new MinLength(array(
			'limit' => 30,
		));
	}

	public function testInvalid()
	{
		$this->assertFalse($this->fixture->validate('A'));
		$this->assertFalse($this->fixture->validate(str_repeat('A', 29)));
	}
	
	public function testValid()
	{
		$this->assertTrue($this->fixture->validate(str_repeat('-', 30)));
	}
	
	public function testLimitChange()
	{
		$this->fixture->limit = 10;
		$this->assertTrue($this->fixture->validate(str_repeat('A', 10)));
		$this->assertTrue($this->fixture->validate(str_repeat('A', 100)));
	}
}