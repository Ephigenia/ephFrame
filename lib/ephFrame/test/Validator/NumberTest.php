<?php

namespace ephFrame\test\Validator;

use ephFrame\Validator\Number;

/**
 * @group Validator
 */
class NumberTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Number();
	}

	public function testNonUnicode()
	{
		$this->fixture->unicode = false;
		$this->assertFalse($this->fixture->validate('⅖'));
		$this->assertTrue($this->fixture->validate('1'));
	}
	
	public function testUnicode()
	{
		$this->fixture->unicode = true;
		$this->assertTrue($this->fixture->validate('⅖'));
		$this->assertTrue($this->fixture->validate('234'));
	}
}