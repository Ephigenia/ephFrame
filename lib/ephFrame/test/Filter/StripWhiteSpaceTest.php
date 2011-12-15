<?php

namespace ephFrame\test\Filter;

use ephFrame\Filter\StripWhiteSpace;

class StripWhiteSpaceTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new StripWhiteSpace();
	}
	
	public function testSimpleValues()
	{
		return array(
			array(' abcd', ' abcd'),
			array("Some Text\r\n\tWith many\r\n\r\nline breaks", 'Some Text With many line breaks'),
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