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
			'<select size="1" name="fieldname"><option value="1">one</option><option value="2" selected="selected">two</option></select>',
			(string) $this->fixture
		);
	}
	
	public function testSelectionChange()
	{
		$this->fixture->submit(1);
		$this->assertEquals(
			'<select size="1" name="fieldname"><option value="1" selected="selected">one</option><option value="2">two</option></select>',
			(string) $this->fixture
		);
	}
	
	public function testMultipleChange()
	{
		$this->fixture->attributes['multiple'] = true;
		$this->assertEquals(
			'<select multiple="multiple" size="1" name="fieldname"><option value="1">one</option><option value="2" selected="selected">two</option></select>',
			(string) $this->fixture
		);
	}
	
	public function testOptGroupRendering()
	{
		$this->fixture->options = array(
			'Numbers' => array('one', 'two', 'tree'),
			'Letters' => array('a', 'b', 3 => 'c')
		);
		$this->assertEquals(
			'<select size="1" name="fieldname"><optgroup label="tree"><option value="0">one</option><option value="1">two</option><option value="2" selected="selected">tree</option></optgroup><optgroup label="c"><option value="0">a</option><option value="1">b</option><option value="3">c</option></optgroup></select>',
			(string) $this->fixture
		);
	}
}