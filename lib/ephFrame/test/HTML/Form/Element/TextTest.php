<?php

namespace ephFrame\test\HTML\Form;

use ephFrame\HTML\Form\Element\Text;

/**
 * @group Element
 */
class TextTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Text('number', 'my value', array(
			'decorators' => false,
			'attributes' => array(
				'maxlength' => 50,
				'size' => 5,
			),
		));
	}
	
	public function test__toString()
	{
		$this->assertEquals(
			'<input maxlength="50" size="5" type="text" name="number" />',
			(string) $this->fixture
		);
	}
}