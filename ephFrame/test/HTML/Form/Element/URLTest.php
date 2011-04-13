<?php

namespace ephFrame\test\HTML\Form;

use ephFrame\HTML\Form\Element\URL;

class URLTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new URL('number');
	}
	
	public function testValidatorError()
	{
		$this->fixture->submit('http://');
		$this->assertTrue((bool) $this->fixture->error());
	}
	
	public function testValidationSuccess()
	{
		$this->fixture->submit('http://www.marceleichner.de');
		$this->assertFalse($this->fixture->error());
	}
	
	public function testDefaultProtocolUsage()
	{
		$this->fixture->submit('www.marceleichner.de');
		$this->assertFalse($this->fixture->error());
	}
	
	public function testDefaultProtocol()
	{
		$this->fixture->defaultProtocol = false;
		$this->fixture->submit('www.marceleichner.de');
		$this->assertTrue((bool) $this->fixture->error());
	}
}