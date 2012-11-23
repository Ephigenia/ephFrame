<?php

namespace ephFrame\test\HTML\Form;

use ephFrame\HTML\Form\Element\Textarea;

/**
 * @group Element
 */
class TextareaTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$Textarea = new Textarea('name', 'value');
		// $this->assertEquals('value', $Textarea->data);
		$this->assertEquals('name', $Textarea->attributes['name']);
	}

	public function testEmptyValue()
	{
		$Textarea = new Textarea('name', null, array(
			'decorators' => false,
			'attributes' => array()
		));
		$this->assertEquals('<textarea name="name"></textarea>', (string) $Textarea);
	}

	public function testMultilineText()
	{
		// test if line breaks in the value get converted to <br />
		$Textarea = new Textarea('text', "Line\nBreaks", array(
			'decorators' => false,
			'attributes' => array()
		));
		$expected = "<textarea name=\"text\">Line\nBreaks</textarea>";
		$this->assertEquals($expected, (string) $Textarea);
	}

	public function testHTMLEncoding()
	{
		// test if line breaks in the value get converted to <br />
		$content = "Something else\n<a href=\"url\"><img src=\"image.jpg\" />My HTML</a>";
		$Textarea = new Textarea('text', $content, array(
			'decorators' => false,
			'attributes' => array()
		));
		$expected = '<textarea name="text">'.htmlspecialchars($content).'</textarea>';
		$this->assertEquals($expected, (string) $Textarea);
	}
}