<?php

namespace ephFrame\test\Form\Element;

use ephFrame\HTML\Form\Element\Number;

/**
 * @group Element
 */
class NumberTest extends \PHPUnit_Framework_TestCase 
{
	public function testFilter()
	{
		$field = new Number('number', '1', array());
		$field->submit(' 12.13 ');
		$this->assertEquals($field->data, '12.13');
	}
}