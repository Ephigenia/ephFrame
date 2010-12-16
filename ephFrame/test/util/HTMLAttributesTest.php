<?php

namespace ephFrame\test\util;

use ephFrame\util\HTMLAttributes;

class HTMLAttributesTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->attributes = new HTMLAttributes(array(
			'href' => 'url',
			'target' => '_blank',
			'title' => '"attribute" & value',
		));
	}
	
	public function test__toString()
	{
		$this->assertEquals((string) $this->attributes, 'href="url" target="_blank" title="&quot;attribute&quot; &amp; value"');
	}
	
	public function testEmptyAttributeValue()
	{
		$attributes = new HTMLAttributes(array(
			'empty' => '',
			'filled' => 'value',
			'emptyarray' => array(' ')
		));
		$this->assertEquals((string) $attributes, 'filled="value"');
	}
	
	public function testValueAsArray()
	{
		$this->attributes->class = array('selected', 'red');
		$this->assertEquals((string) $this->attributes, 'href="url" target="_blank" title="&quot;attribute&quot; &amp; value" class="selected red"');
	}
	
	public function testValueArrayConvert()
	{
		$this->attributes->class = 'selected';
		$this->attributes->class = array_merge((array) $this->attributes->class, array('red'));
		$this->assertEquals((string) $this->attributes, 'href="url" target="_blank" title="&quot;attribute&quot; &amp; value" class="selected red"');
	}
	
	public function testValueAppend()
	{
		$this->attributes->class = 'selected';
		$this->attributes->class .= ' red';
		$this->assertEquals((string) $this->attributes, 'href="url" target="_blank" title="&quot;attribute&quot; &amp; value" class="selected red"');
	}
	
	public function testChange()
	{
		$this->attributes->href = 'http://www.ephigenia.de';
		$this->assertEquals((string) $this->attributes, 'href="http://www.ephigenia.de" target="_blank" title="&quot;attribute&quot; &amp; value"');
	}
	
	public function testDelete()
	{
		unset($this->attributes->href);
		unset($this->attributes->title);
		$this->assertEquals((string) $this->attributes, 'target="_blank"');
	}
	
	public function testAdd()
	{
		$this->attributes->rel = 'external';
		$this->assertEquals((string) $this->attributes, 'href="url" target="_blank" title="&quot;attribute&quot; &amp; value" rel="external"');
	}
}