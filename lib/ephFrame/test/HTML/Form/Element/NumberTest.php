<?php

namespace ephFrame\test\HTML\Form;

use ephFrame\HTML\Form\Element\Number;

/**
 * @group Element
 */
class NumberTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Number('number');
	}
	
	public function testValidatorError()
	{
		$this->fixture->submit('asldkj');
		$this->assertTrue((bool) $this->fixture->error());
	}
	
	public function testValidationSuccess()
	{
		$this->fixture->submit(123);
		$this->assertFalse($this->fixture->error());
	}
	
	public function testFilter()
	{
		$field = new Number('number', '1', array());
		$field->submit(' 12.13 ');
		$this->assertEquals('12.13', $field->data);
	}
}