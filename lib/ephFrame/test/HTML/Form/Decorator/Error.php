<?php

namespace ephFrame\test\HTML\Form\Decorator;

use
	ephFrame\HTML\Form\Decorator\Error,
	ephFrame\HTML\Form\Decorator\Position,
	ephFrame\HTML\Form\Element\Text
	;

/**
 * @group Form
 * @group FormDecorator
 */
class ErrorTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Text('inputfield1', 'value', array(
			'decorators' => new Error(null, array('class' => 'errormsg')),
		));
	}
	
	public function testOptionsMergeConstruct()
	{
		$this->fixture->errors[] = 'this is an error message';
		$this->fixture->decorators = array(
			new Error($this->fixture, array(
				'position' => Position::PREPEND,
				'attributes' => array(
					'style' => 'border: 1px solid red;',
					'class' => 'error'
				)
			))
		);
		$this->assertEquals(
			'<p class="error" style="border: 1px solid red;">this is an error message</p>'.
			'<input type="text" name="inputfield1" value="value" />',
			(string) $this->fixture
		);
	}
	
	public function testNoErrorRendering()
	{
		$this->assertEquals(
			'<input type="text" name="inputfield1" value="value" />',
			(string) $this->fixture
		);
	}
	
	public function testErrorRendering()
	{
		$this->fixture->errors[] = 'this is an error message';
		$this->fixture->tag = 'span';
		$this->assertEquals(
			'<input type="text" name="inputfield1" value="value" />'.
			'<span class="errormsg">this is an error message</span>',
			(string) $this->fixture
		);
	}
	
	public function testPositionChange()
	{
		$this->fixture->errors[] = 'this is an error message';
		$this->fixture->position = Position::PREPEND;
		$this->fixture->attributes['style'] = 'border: 1px solid red;';
		$this->assertEquals(
			'<p class="errormsg" style="border: 1px solid red;">this is an error message</p>'.
			'<input type="text" name="inputfield1" value="value" />',
			(string) $this->fixture
		);
	}
}