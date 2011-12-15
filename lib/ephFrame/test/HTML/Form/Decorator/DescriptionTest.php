<?php

namespace ephFrame\test\HTML\Form\Decorator;

use 
	ephFrame\HTML\Form\Decorator\Description,
	ephFrame\HTML\Form\Element\Text
	;

/**
 * @group Form
 * @group FormDecorator
 */
class DescriptionTest extends \PHPUnit_Framework_TestCase
{
	public function setUp() {
		$this->fixture = new Text('field1', 'myvalue', array(
			'decorators' => array(
				new Description(null, array(
					'tag' => 'span',
				)),
			),
			'description' => 'describe & explain',
		));
	}
	
	public function test__toString()
	{
		$this->assertEquals(
			'<input type="text" maxlength="255" name="field1" /><span class="description">describe & explain</span>',
			(string) $this->fixture
		);
	}
	
	public function testNoDescriptionRendering()
	{
		$this->fixture->description = false;
		$this->assertEquals(
			'<input type="text" maxlength="255" name="field1" />',
			(string) $this->fixture
		);
	}
}