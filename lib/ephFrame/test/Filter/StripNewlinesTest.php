<?php

namespace ephFrame\test\Filter;

use ephFrame\Filter\StripNewlines;

/**
 * @group Filter
 */
class StripNewlinesTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new StripNewlines();
	}
	
	public function testSimpleValues()
	{
		return array(
			array('abc', 'abc'),
			array("a\rb\nc\r\n\n\r", 'abc'),
		);
	}
	
	/**
	 * @dataProvider testSimpleValues()
	 */
	public function testSimple($left, $right)
	{
		$this->assertEquals($right, $this->fixture->apply($left));
	}
}