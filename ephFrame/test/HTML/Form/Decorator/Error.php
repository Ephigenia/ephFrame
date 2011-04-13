<?php

namespace ephFrame\test\HTML\Form\Decorator;

use ephFrame\HTML\Form\Decorator\Error;
use ephFrame\HTML\Form\Decorator\DecoratorPosition;
use ephFrame\HTML\Form\Element\Text;

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
				'position' => DecoratorPosition::PREPEND,
				'attributes' => array(
					'style' => 'border: 1px solid red;',
					'class' => 'error'
				)
			))
		);
		$this->assertEquals(
			(string) $this->fixture,
			'<p class="error" style="border: 1px solid red;">this is an error message</p>'.
			'<input type="text" name="inputfield1" value="value" />'
		);
	}
	
	public function testNoErrorRendering()
	{
		$this->assertEquals(
			(string) $this->fixture,
			'<input type="text" name="inputfield1" value="value" />',
		);
	}
	
	public function testErrorRendering()
	{
		$this->fixture->errors[] = 'this is an error message';
		$this->fixture->tag = 'span';
		$this->assertEquals(
			(string) $this->fixture,
			'<input type="text" name="inputfield1" value="value" />'.
			'<span class="errormsg">this is an error message</span>',
		);
	}
	
	public function testPositionChange()
	{
		$this->fixture->errors[] = 'this is an error message';
		$this->fixture->position = DecoratorPosition::PREPEND;
		$this->fixture->attributes['style'] = 'border: 1px solid red;';
		$this->assertEquals(
			(string) $this->fixture,
			'<p class="errormsg" style="border: 1px solid red;">this is an error message</p>'.
			'<input type="text" name="inputfield1" value="value" />'
		);
	}
}