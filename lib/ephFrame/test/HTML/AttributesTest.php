<?php

namespace ephFrame\test\HTML;

use ephFrame\HTML\Attributes;

class AttributesTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->attributes = new Attributes(array(
			'href' => 'url',
			'target' => '_blank',
			'title' => '"attribute" & value',
		));
	}
	
	public function test__toString()
	{
		$this->assertEquals(
			'href="url" target="_blank" title="&quot;attribute&quot; &amp; value"',
			(string) $this->attributes
		);
	}
	
	public function testPropertyAccess()
	{
		$this->assertEquals($this->attributes->href, 'url');
	}
	
	public function testEmptyAttributeValue()
	{
		$attributes = new Attributes(array(
			'empty' => '',
			'filled' => 'value',
			'emptyarray' => array(' ')
		));
		$this->assertEquals(
			'filled="value"',
			(string) $attributes
		);
	}
	
	public function testValueAsArray()
	{
		$this->attributes->class = array('selected', 'red');
		$this->assertEquals(
			'href="url" target="_blank" title="&quot;attribute&quot; &amp; value" class="selected red"',
			(string) $this->attributes
		);
	}
	
	public function testValueArrayConvert()
	{
		$this->attributes->class = 'selected';
		$this->attributes->class = array_merge((array) $this->attributes->class, array('red'));
		$this->assertEquals(
			'href="url" target="_blank" title="&quot;attribute&quot; &amp; value" class="selected red"',
			(string) $this->attributes
		);
	}
	
	public function testValueAppend()
	{
		$this->attributes->class = 'selected';
		$this->attributes->class .= ' red';
		$this->assertEquals(
			'href="url" target="_blank" title="&quot;attribute&quot; &amp; value" class="selected red"',
			(string) $this->attributes
		);
	}
	
	public function testValueDoubledValue()
	{
		$this->attributes->class = array('selected', 'selected', 'red');
		$this->assertEquals(
			'href="url" target="_blank" title="&quot;attribute&quot; &amp; value" class="selected red"',
			(string) $this->attributes
		);
	}
	
	public function testValueDoubledValueWithDifferentKeys()
	{
		$this->attributes->class = 'selected';
		$this->attributes->id = 'selected';
		$this->assertEquals(
			'href="url" target="_blank" title="&quot;attribute&quot; &amp; value" class="selected" id="selected"',
			(string) $this->attributes
		);
	}
	
	public function testChange()
	{
		$this->attributes->href = 'http://www.ephigenia.de';
		$this->assertEquals(
			'href="http://www.ephigenia.de" target="_blank" title="&quot;attribute&quot; &amp; value"',
			(string) $this->attributes
		);
	}
	
	public function testDelete()
	{
		unset($this->attributes->href);
		unset($this->attributes->title);
		$this->assertEquals(
			'target="_blank"',
			(string) $this->attributes
		);
	}
	
	public function testAdd()
	{
		$this->attributes->rel = 'external';
		$this->assertEquals(
			'href="url" target="_blank" title="&quot;attribute&quot; &amp; value" rel="external"',
			(string) $this->attributes
		);
	}
}