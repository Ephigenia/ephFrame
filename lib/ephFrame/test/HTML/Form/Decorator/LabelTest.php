<?php

namespace ephFrame\test\HTML\Form\Decorator;

use
	ephFrame\HTML\Form\Decorator\Label,
	ephFrame\HTML\Form\Element\Text
	;

/**
 * @group Form
 * @group FormDecorator
 */
class LabelTest extends \PHPUnit_Framework_TestCase
{
	public function setUp() {
		$this->fixture = new Text('fieldname', 'value', array(
			'decorators' => array(
				'label' => new Label(),
			),
		));
	}
	
	public function test__toString()
	{
		$this->assertEquals(
			'<label>fieldname:</label><input type="text" maxlength="255" name="fieldname" />',
			(string) $this->fixture
		);
	}
	
	public function testFormat()
	{
		$this->fixture->decorators['label']->format = '%s (optional):';
		$this->assertEquals(
			'<label>fieldname (optional):</label><input type="text" maxlength="255" name="fieldname" />',
			(string) $this->fixture
		);
	}
	
	public function testAdditionalAttributes()
	{
		$this->fixture->decorators['label']->attributes['onclick'] = 'javascript';
		$this->assertEquals(
			'<label onclick="javascript">fieldname:</label><input type="text" maxlength="255" name="fieldname" />',
			(string) $this->fixture
		);
	}
	
	public function testEmptyString()
	{
		$this->fixture->label == '';
		$this->assertEquals(
			'<label>fieldname:</label><input type="text" maxlength="255" name="fieldname" />',
			(string) $this->fixture
		);
	}
	
	public function testFalse()
	{
		$this->fixture->label = false;
		$this->assertEquals(
			'<input type="text" maxlength="255" name="fieldname" />',
			(string) $this->fixture
		);
	}
}