<?php

namespace ephFrame\test\HTML\Form;

use ephFrame\HTML\Form\Element\Email;

class EmailTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Email('email');
	}
	
	public function testValidationSuccess()
	{
		$this->assertFalse($this->fixture->validate('no valid email'));
	}
}