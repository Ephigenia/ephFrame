<?php

namespace ephFrame\test\Validator;

use ephFrame\Validator\MaxLength;

class MaxLengthTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new MaxLength(array(
			'limit' => 30,
		));
	}

	public function testInvalid()
	{
		$this->assertFalse($this->fixture->validate(str_repeat('-', 31)));
	}
	
	public function testValid()
	{
		$this->assertTrue($this->fixture->validate(str_repeat('-', 1)));
		$this->assertTrue($this->fixture->validate(str_repeat('-', 30)));
	}
	
	public function testLimitChange()
	{
		$this->fixture->limit = 40;
		$this->assertTrue($this->fixture->validate(str_repeat('-', 20)));
		$this->assertTrue($this->fixture->validate(str_repeat('-', 40)));
	}
	
	public function testMessageSubstitution()
	{
		$this->assertEquals(
			$this->fixture->message(),
			'This value is too long. It should have 30 characters or less'
		);
	}
}