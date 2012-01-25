<?php

namespace ephFrame\test\Filter;

use \ephFrame\Filter\Float;

class FloatTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Float();
	}
	
	public function testSimpleValues()
	{
		return array(
			array(true, 1.),
			array('1', 1.),
			array('-1', -1.),
			array('a1', 1),
			array('1.2', 1.2),
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