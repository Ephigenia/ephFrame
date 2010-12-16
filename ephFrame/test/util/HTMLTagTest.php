<?php

namespace ephFrame\test\util;

use ephFrame\util\HTMLTag;

class HTMLTagTest extends \PHPUnit_Framework_TestCase
{
	public function toStringComparisonValues()
	{
		return array(
			array(
				'a', 'link label', array('href' => 'http://www.ephigenia.de'),
				'<a href="http://www.ephigenia.de">link label</a>',
			),
			array(
				'strong', 'hip & hop', array(),
				'<strong>hip &amp; hop</strong>',
			),
			array(
				'br', null, array(),
				'<br />',
			),
			array(
				'meta', null, array('href' => 'style.css', 'type' => 'text/css'),
				'<meta href="style.css" type="text/css" />',
			),
		);
	}
	
	/**
	 * @dataProvider toStringComparisonValues
	 */
	public function test__toString($tag, $label, $attributes, $result)
	{
		$tag = new HTMLTag($tag, $label, $attributes);
		$this->assertEquals((string) $tag, $result);
	}
	
	public function testSimpleNested()
	{
		$tag1 = new HTMLTag('img', null, array('src' => 'image.jpg'));
		$tag2 = new HTMLTag('a', $tag1, array('rel' => 'external'));
		$this->assertEquals((string) $tag2, '<a rel="external"><img src="image.jpg" /></a>');
	}
}