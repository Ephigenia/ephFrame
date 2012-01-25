<?php

namespace ephFrame\test\Filter;

use ephFrame\Filter\Boolean;

class BooleanTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Boolean();
	}
	
	public function testSimpleValues()
	{
		return array(
			array(true, true),
			array(false, false),
			array(1, true),
			array('1', true),
			array(array(), false),
			array(array(1), true),
			array(null, false),
			array('', false),
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