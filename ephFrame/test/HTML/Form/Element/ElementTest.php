<?php

namespace ephFrame\test\HTML\Form;

use ephFrame\HTML\Form\Element\Element;
use ephFrame\Validator\Integer;

class ElementTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Element('fieldname', 'value', array(
			'attributes' => array(
				'class' => 'pink',
			),
			'validators' => array(
				new Integer(),
			)
		));
	}
	public function testAttributesInConstructor()
	{
		$this->assertEquals($this->fixture->attributes['class'], 'pink');
		$this->assertEquals($this->fixture->attributes['name'], 'fieldname');
	}
	
	public function testValidate()
	{
		$this->assertFalse($this->fixture->validate('string'));
	}
	
	public function testError()
	{
		$this->fixture->submit(12323);
		$this->assertFalse($this->fixture->error());
		$this->assertTrue($this->fixture->ok());
	}
	
	public function testNoDecorators()
	{
		$element = new Element('field1', true, array('decorators' => false));
		$this->assertFalse($element->decorators);
	}
	
	public function testSubmit()
	{
		$element = new Element('field1', true, array('decorators' => false));
		$this->assertTrue(empty($element->data));
		$element->submit('myvalue');
		$this->assertEquals($element->data, 'myvalue');
	}
}