<?php

namespace ephFrame\test\Validator;

use ephFrame\Validator\Length;

/**
 * @group Validator
 */
class LengthTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Length(array(
			'length' => 10,
		));
	}

	public function testInvalid()
	{
		$this->assertFalse($this->fixture->validate(str_repeat('-', 9)));
	}
	
	public function testValid()
	{
		$this->assertTrue($this->fixture->validate(str_repeat('-', 10)));
	}
	
	public function testLimitChange()
	{
		$this->fixture->length = 40;
		$this->assertTrue($this->fixture->validate(str_repeat('-', 40)));
	}
	
	public function testMessageSubstitution()
	{
		$this->assertEquals(
			$this->fixture->message(),
			'This value should be exact 10 characters in length.'
		);
	}
}