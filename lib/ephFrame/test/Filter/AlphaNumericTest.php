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
			array(0123.0, '123'),
			array(0x00, '0'),
			array(false, ''),
		);
	}
	
	/**
	 * @dataProvider testSimpleValues()
	 */
	public function testSimple($input, $output)
	{
		$this->assertEquals($this->fixture->apply($input), $output);
	}
	
	public function testUnicodeNumericValues()
	{
		$arabic = '١';// arabic two
		$bengali = '৭'; // begali seven
		return array(
			array('errors: '.$arabic, 'errors: '.$arabic), 
			array('eggs: '.$bengali, 'eggs: '.$bengali),
		);
	}
	
	/**
	 * @dataProvider testUnicodeNumericValues()
	 */
	public function testUnicodeNumeric($left, $right)
	{
		$this->fixture->unicode = true;
		$this->fixture->whitespace = true;
		$this->fixture->chars = array(':');
		$this->assertEquals($this->fixture->apply($left), $right);
	}
	
	public function testArray()
	{
		$array = array('a', 'b', 'c', 1, 2, 3);
		$this->assertEquals($this->fixture->apply($array), $array);
	}
}