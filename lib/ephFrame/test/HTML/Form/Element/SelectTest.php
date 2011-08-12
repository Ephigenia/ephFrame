<?php

namespace ephFrame\test\HTML\Form;

use ephFrame\HTML\Form\Element\Select;

/**
 * @group Element
 */
class SelectTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Select('fieldname',2, array(
			'options' => array(
				1 => 'one',
				2 => 'two',
			),
			'decorators' => false,
		));
	}
	
	public function test__toString()
	{
		$this->assertEquals(
			(string) $this->fixture,
			'<select size="1" name="fieldname"><option value="1">one</option><option value="2" selected="selected">two</option></select>'
		);
	}
	
	public function testSelectionChange()
	{
		$this->fixture->submit(1);
		$this->assertEquals(
			(string) $this->fixture,
			'<select size="1" name="fieldname"><option value="1" selected="selected">one</option><option value="2">two</option></select>'
		);
	}
	
	public function testMultipleChange()
	{
		$this->fixture->attributes['multiple'] = true;
		$this->assertEquals(
			(string) $this->fixture,
			'<select multiple="multiple" size="1" name="fieldname"><option value="1">one</option><option value="2" selected="selected">two</option></select>'
		);
	}
}