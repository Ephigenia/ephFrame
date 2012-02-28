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
		$this->decorator = new Error(null, array(
			'attributes' => array(
				'class' => 'errormsg'
			)
		));
		$this->fixture = new Text('inputfield1', 'value', array(
			'decorators' => array($this->decorator)
		));
	}
	
	public function testMultipleMessages()
	{
		$this->fixture->errors[] = 1;
		$this->fixture->errors[] = 2;
		$this->assertEquals(
			'<input type="text" maxlength="255" name="inputfield1" />'.
			'<p class="errormsg">1'.PHP_EOL.'2</p>',
			(string) $this->fixture
		);
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
			'<p style="border: 1px solid red;" class="error">this is an error message</p>'.
			'<input type="text" maxlength="255" name="inputfield1" />',
			(string) $this->fixture
		);
	}
	
	public function testNoErrorRendering()
	{
		$this->assertEquals(
			'<input type="text" maxlength="255" name="inputfield1" />',
			(string) $this->fixture
		);
	}
	
	public function testErrorRendering()
	{
		$this->fixture->errors[] = 'this is an error message';
		$this->decorator->tag = 'span';
		$this->assertEquals(
			'<input type="text" maxlength="255" name="inputfield1" />'.
			'<span class="errormsg">this is an error message</span>',
			(string) $this->fixture
		);
	}
	
	public function testPositionChange()
	{
		$this->fixture->errors[] = 'this is an error message';
		$this->fixture->position = Position::PREPEND;
		$this->assertEquals(
			'<input type="text" maxlength="255" name="inputfield1" />'.
			'<p class="errormsg">this is an error message</p>',
			(string) $this->fixture
		);
	}
}