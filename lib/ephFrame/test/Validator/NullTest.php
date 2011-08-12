<?php

namespace ephFrame\test\Validator;

use ephFrame\Validator\Null;

/**
 * @group Validator
 */
class NullTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Null();
	}
	
	public function testInvalidValues()
	{
		return array(
			array(''),
			array('asd'),
			array(new Null()),
			array('  '),
		);
	}
	
	/**
	 * @dataProvider testInvalidValues()
	 */
	public function testInvalid($value)
	{
		$this->assertFalse($this->fixture->validate($value));
	}
	
	public function testValidValues()
	{
		return array(
			array(null), 
		);
	}
	
	/**
	 * @dataProvider testValidValues()
	 */
	public function testValid($value)
	{
		$this->assertTrue($this->fixture->validate($value));
	}
}