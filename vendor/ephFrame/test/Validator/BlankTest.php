<?php

namespace ephFrame\test\Validator;

use ephFrame\Validator\Blank;

class BlankTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Blank();
	}
	
	public function testInvalidValues()
	{
		return array(
			array('a'), 
			array(false),
			array(array())
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
			array(''),
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