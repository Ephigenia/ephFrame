<?php

namespace ephFrame\test\HTML\Form;

use
	ephFrame\HTML\Form\Element\Element,
	ephFrame\validator\Integer
	;

/**
 * @group Form
 * @group Element
 */
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
	
	public function testAutoName()
	{
		$field = new Element(null, 'myvalue');
		$this->assertEquals($field->attributes['name'], 'element');
	}
	
	public function testRequiredWithEmptyData()
	{
		$this->assertFalse($this->fixture->submit(Null)->ok());
	}
	
	public function testRequiredWithValue()
	{
		$this->fixture->required = false;
		$this->assertTrue($this->fixture->submit('value')->ok());
	}
	
	public function testTrim()
	{
		$this->assertEquals(
			'trim me',
			$this->fixture->submit('  trim me ')->data
		);
	}
	
	public function testStripTags()
	{
		$this->assertEquals(
			$this->fixture->submit('<em><strong>strong</strong> value</em>')->data,
			'strong value'
		);
	}
	
	public function testAttributesInConstructor()
	{
		$this->assertEquals('pink', $this->fixture->attributes['class']);
		$this->assertEquals('fieldname', $this->fixture->attributes['name']);
	}
	
	public function testAttributesAdd()
	{
		$value = '1px solid red';
		$this->fixture->attributes['style'] = $value;
		$this->assertEquals($value, $this->fixture->attributes['style']);
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
		$this->assertEquals('myvalue', $element->data);
	}
}