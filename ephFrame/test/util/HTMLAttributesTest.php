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
	
	public function testChange()
	{
		$this->attributes->href = 'http://www.ephigenia.de';
		$this->assertEquals((string) $this->attributes, 'href="http://www.ephigenia.de" target="_blank" title="&quot;attribute&quot; &amp; value"');
	}
	
	public function testAdd()
	{
		$this->attributes->rel = 'external';
		$this->assertEquals((string) $this->attributes, 'href="url" target="_blank" title="&quot;attribute&quot; &amp; value" rel="external"');
	}
}