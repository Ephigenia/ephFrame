<?php

namespace ephFrame\test\HTML\Form;

use ephFrame\HTML\Form\Element\Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Text('number', 'my value', array(
			'decorators' => false,
			'attributes' => array(
				'max_length' => 50,
				'size' => 10,
			),
		));
	}
	
	public function test__toString()
	{
		$this->assertEquals(
			(string) $this->fixture,
			'<input type="text" maxlength="255" name="number" max_length="50" size="10" />'
		);
	}
}