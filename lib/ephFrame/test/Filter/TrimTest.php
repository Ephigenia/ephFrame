<?php

namespace ephFrame\test\Filter;

use ephFrame\Filter\Trim;

class TrimTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Trim();
	}
	
	public function testSimpleValues()
	{
		return array(
			array(' abcd', 'abcd'),
			array("\r\ttest", 'test'),
			array("\rtest a\t\r", 'test a'),
		);
	}
	
	/**
	 * @dataProvider testSimpleValues()
	 */
	public function testSimple($left, $right)
	{
		$this->assertEquals($right, $this->fixture->apply($left));
	}
	
	public function testCharsArray()
	{
		$this->fixture->chars = array('a', 'c');
		$this->assertEquals('bcSTAY',$this->fixture->apply('abcSTAYac'));
	}
	
	public function testCharsString()
	{
		$this->fixture->chars = 'ac';
		$this->assertEquals('bcSTAY', $this->fixture->apply('abcSTAYac'));
		$this->fixture->chars = 'acY';
		$this->assertEquals('bcSTA', $this->fixture->apply('abcSTAYac'));
	}
}