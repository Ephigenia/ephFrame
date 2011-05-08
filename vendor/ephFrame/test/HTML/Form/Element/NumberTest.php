<?php

namespace ephFrame\test\HTML\Form;

use ephFrame\HTML\Form\Element\Number;

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
}