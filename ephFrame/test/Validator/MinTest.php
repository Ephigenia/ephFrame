<?php

namespace ephFrame\test\Validator;

use ephFrame\Validator\Min;

class MinTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Min(array(
			'limit' => 30,
		));
	}

	public function testInvalid()
	{
		$this->assertFalse($this->fixture->validate(29));
		$this->assertFalse($this->fixture->validate(29.99999999));
	}

	public function testValid()
	{
		$this->assertTrue($this->fixture->validate(30));
		$this->assertTrue($this->fixture->validate(30.00000001));
	}
	
	public function testLimitChange()
	{
		$this->fixture->limit = 10;
		$this->assertTrue($this->fixture->validate(10));
		$this->assertTrue($this->fixture->validate(11));
		$this->assertFalse($this->fixture->validate(9));
	}
}