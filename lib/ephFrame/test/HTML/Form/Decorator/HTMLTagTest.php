<?php

namespace ephFrame\test\HTML\Form\Decorator;

use
	ephFrame\HTML\Form\Decorator\HTMLTag,
	ephFrame\HTML\Form\Decorator\Position,
	ephFrame\HTML\Form\Element\Text
	;

/**
 * @group Form
 * @group FormDecorator
 */
class HTMLTagTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->decorator = new HTMLTag();
		$this->fixture = new Text('inputfield1', 'value', array(
			'decorators' => array($this->decorator)
		));
	}
	
	public function testDefaultRendering()
	{
		$this->assertEquals('<div class="text"><input type="text" maxlength="255" name="inputfield1" /></div>', (string) $this->fixture);
	}
	
	public function testWithClassAsString()
	{
		$this->decorator->attributes['class'] = 'username';
		$this->assertEquals('<div class="username text"><input type="text" maxlength="255" name="inputfield1" /></div>', (string) $this->fixture);
	}
	
	public function testOtherPositionAndValue()
	{
		$this->decorator->value = 'Username:';
		$this->decorator->position = Position::INSERT_AFTER;
		$this->decorator->addElementClass = false;
		$this->assertEquals('<input type="text" maxlength="255" name="inputfield1"><div>Username:</div></input>', (string) $this->fixture);
	}
	
	public function testEmptyValue()
	{
		$this->decorator->value = false;
		$this->decorator->position = Position::APPEND;
		$this->assertEquals('<input type="text" maxlength="255" name="inputfield1" />', (string) $this->fixture);
	}
}