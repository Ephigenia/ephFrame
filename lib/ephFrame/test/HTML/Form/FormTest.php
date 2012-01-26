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
			(string) $this->fixture,
			'<form method="post" accept-charset="utf-8" action="myaction"><fieldset /></form>'
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
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testOffsetUnset()
	{
		$element = new Text('field1', 'value');
		$this->fixture['text'] = $element;
		unset($this->fixture['text']);
		$this->fixture['text'];
		$element = new Text('field1', 'value');
		$this->fixture['text'] = $element;
		unset($this->fixture['field1']);
		$this->fixture['field1'];
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