<?php

namespace ephFrame\test\validator;

use ephFrame\validator\Regexp;

/**
 * @group Validator
 */
class RegexpTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Regexp(array(
			'regexp' => '@[A-Z]{2,3}@'
		));
	}
	
	public function testInvalid()
	{
		$this->assertFalse($this->fixture->validate('-.'));
	}
	
	public function testValid()
	{
		$this->assertTrue($this->fixture->validate('AVC'));
	}
	
	public function testRegexpChange()
	{
		$this->fixture->regexp = '@\d+@';
		$this->assertTrue($this->fixture->validate('1212'));
		$this->assertFalse($this->fixture->validate('asdsd'));
	}
}