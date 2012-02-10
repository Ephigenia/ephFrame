<?php

namespace ephFrame\test\validator;

use ephFrame\validator\NotEmpty;

/**
 * @group Validator
 */
class NotEmptyTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new NotEmpty();
	}
	
	public function testInvalidValues()
	{
		return array(
			array(''),
			array(null),
			array(false),
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
			array('a'), 
			array(true),
			array(array('1')),
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