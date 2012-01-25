<?php

namespace ephFrame\test\Filter;

use \ephFrame\Filter\Number;

class NumberTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Number();
	}
	
	public function testSimpleValues()
	{
		return array(
			array(true, '1'),
			array(1, '1'),
			array('a2', '2'),
			array('this is 1 to many 4 you', '14'),
		);
	}
	
	/**
	 * @dataProvider testSimpleValues()
	 */
	public function testSimple($left, $right)
	{
		$this->fixture->unicode = false;
		$this->assertEquals($right, $this->fixture->apply($left));
	}
	
	public function testUnicodeNumericValues()
	{
		$arabic = '١';// arabic two
		$bengali = '৭'; // begali seven
		return array(
			array($arabic, $arabic), 
			array($bengali, $bengali),
		);
	}
	
	/**
	 * @dataProvider testUnicodeNumericValues()
	 */
	public function testUnicodeNumeric($left, $right)
	{
		$this->fixture->unicode = true;
		$this->assertEquals($right, $this->fixture->apply($left));
	}
	
	public function testWhitespace()
	{
		$this->assertEquals('   1', $this->fixture->apply('   char1chars'));
	}
}