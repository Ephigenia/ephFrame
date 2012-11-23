<?php

namespace ephFrame\test\HTML\Form;

use
	ephFrame\HTML\Form\Form,
	ephFrame\HTML\Form\Element\Text
	;

/**
 * @group Form
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Form(array('action' => 'myaction'));
	}
	
	public function test__toString()
	{
		$this->assertEquals(
			'<form method="post" accept-charset="utf-8" action="myaction"><fieldset></fieldset></form>',
			(string) $this->fixture
		);
	}
	
	public function testOffsetSetAndGet()
	{
		$element = new Text('field1', 'value');
		$this->fixture['text'] = $element;
		$this->assertEquals($element, $this->fixture['text']);
		$this->assertEquals($element, $this->fixture->text);
		$this->fixture['field1'] = $element;
		$this->assertEquals($element, $this->fixture->field1);
		
		$this->fixture->fieldsets[0][] = new Text('newname', 'value');
		$this->assertTrue((bool) $this->fixture['newname']);
		
		$this->fixture->fieldsets[0]['textfield'] = $textField = new Text('textfield');
		$this->fixture['textfield'] = $newTextField =  new Text('newname');
		$this->assertEquals($newTextField, $this->fixture['textfield']);
	}
	
	public function testError()
	{
		$this->fixture->errors[] = 'Something bad';
		$this->assertTrue((bool) $this->fixture->error());
	}
	
	public function testOk()
	{
		$this->assertTrue($this->fixture->ok());
	}
	
	public function testFieldError()
	{
		$textField = new Text('text');
		$this->fixture['text'] = $textField;
		$textField->submit('');
		$this->assertFalse($this->fixture->ok());
		// test with now valid field
		$textField->submit('OK');
		$this->fixture->errors[] = 'But there is still something missing';
		$this->assertFalse($this->fixture->ok());
	}
	
	public function testFieldInvalidates()
	{
		$textField = new Text('text');
		$this->fixture['text'] = $textField;
		$textField->submit('');
		$this->assertFalse($this->fixture->isValid());
		$textField->submit('OK');
		$this->assertTrue($this->fixture->isValid());
	}
	
	public function testOffsetUnset()
	{
		$element = new Text('field1', 'value');
		$this->fixture['text'] = $element;
		unset($this->fixture['text']);
	}
	
	public function testOffsetUnsetByName()
	{
		$element = new Text('text');
		$this->fixture->fieldsets[0][] = $element;
		unset($this->fixture['text']);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testOffsetUnsetException()
	{
		unset($this->fixture['not_existent_field']);
	}
	
	public function testOffsetSetByName()
	{
		
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testOffsetSetException()
	{
		$this->fixture['error'] = 'Exception';
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testOffsetGetException()
	{
		$r = $this->fixture['error'];
	}
	
	/**
	 * @depends testOffsetSetAndGet
	 */
	public function testBind()
	{
		$this->fixture['text'] = new Text('field1', 'value');
		$value = 'my Value';
		$this->fixture->bind(array('field1' => $value));
		$this->assertEquals($value, $this->fixture['text']->data);
	}
	
	/**
	 * @depends testOffsetSetAndGet
	 */
	public function testToArray()
	{
		$this->fixture['text'] = new Text('field1', 'value');
		$this->fixture->bind(array('field1' => 'newValue'));
		$this->assertEquals(array('field1' => 'newValue'), $this->fixture->toArray());
	}
}