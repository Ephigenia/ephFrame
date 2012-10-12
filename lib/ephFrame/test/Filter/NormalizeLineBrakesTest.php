<?php

namespace ephFrame\test\Filter;

use ephFrame\Filter\NormalizeLineBrakes;

/**
 * @group Filter
 */
class NormalizeLineBrakesTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new NormalizeLineBrakes();
	}
	
	public function testSimpleValues()
	{
		return array(
			array(' abcd', ' abcd'),
			array("\r\ttest", "\n\ttest"),
			array("A\n\tB\r\nC\n\nD\n\rE", "A\n\tB\nC\n\nD\nE"),
		);
	}
	
	/**
	 * @dataProvider testSimpleValues()
	 */
	public function testSimple($left, $right)
	{
		$this->assertEquals($right,$this->fixture->apply($left));
	}
}