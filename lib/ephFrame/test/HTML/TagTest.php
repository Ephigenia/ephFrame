<?php

namespace ephFrame\test\HTML;

use ephFrame\HTML\Tag;

class TagTest extends \PHPUnit_Framework_TestCase
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
			array(
				'strong', 'Hänsel und Gretel', array(),
				'<strong>Hänsel und Gretel</strong>',
			),
		);
	}
	
	/**
	 * @dataProvider toStringComparisonValues
	 */
	public function test__toString($tag, $label, $attributes, $expected)
	{
		$tag = new Tag($tag, $label, $attributes);
		$this->assertEquals($expected, (string) $tag);
	}
	
	public function testValueAsArray()
	{
		$tag = new Tag('p', array(
			new Tag('strong', 'text'),
			new Tag('em', 'something'),
		));
		$this->assertEquals(
			'<p><strong>text</strong><em>something</em></p>',
			(string) $tag
		);
	}
	
	public function testSimpleNested()
	{
		$tag1 = new Tag('img', null, array('src' => 'image.jpg'));
		$tag2 = new Tag('a', $tag1, array('rel' => 'external'));
		$this->assertEquals(
			'<a rel="external"><img src="image.jpg" /></a>',
			(string) $tag2
		);
	}
}