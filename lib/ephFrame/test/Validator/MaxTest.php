<?php

namespace ephFrame\test\Validator;

use ephFrame\Validator\Max;

/**
 * @group Validator
 */
class MaxTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Max(array(
			'limit' => 30,
		));
	}

	public function testInvalid()
	{
		$this->assertFalse($this->fixture->validate(31));
		$this->assertFalse($this->fixture->validate(30.00000000001));
	}

	public function testValid()
	{
		$this->assertTrue($this->fixture->validate(30));
		$this->assertTrue($this->fixture->validate(29));
	}
	
	public function testLimitChange()
	{
		$this->fixture->limit = 10;
		$this->assertTrue($this->fixture->validate(10));
		$this->assertTrue($this->fixture->validate(9));
		$this->assertFalse($this->fixture->validate(11));
	}
}