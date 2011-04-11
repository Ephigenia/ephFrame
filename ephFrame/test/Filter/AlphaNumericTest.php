<?php

namespace ephFrame\test\Filter;

use ephFrame\Filter\AlphaNumeric;

class AlphaNumericTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new AlphaNumeric(array('unicode' => false, 'whitespace' => false));
	}
	
	public function testConstructor()
	{
		$this->assertFalse($this->fixture->unicode);
		$this->assertFalse($this->fixture->whitespace);
	}
	
	public function testSimpleValues()
	{
		return array(
			array(' ab 12-', 'ab12'),
			array('12,34.00', '123400'),
			array(0123.0, '1230'),
			array(0, '0'),
			array(false, ''),
			array(array(), ''),
		);
	}
}