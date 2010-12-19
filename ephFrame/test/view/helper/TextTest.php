<?php

namespace ephFrame\test\view\helper;

use ephFrame\view\helper\Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Text();
	}
	
	public function testAutoURLEqualValues()
	{
		return array(
			array(
				'http://www.ephigenia.de',
				'<a href="http://www.ephigenia.de">http://www.ephigenia.de</a>',
			),
			array(
				'ftp://www.ephigenia.de',
				'<a href="ftp://www.ephigenia.de">ftp://www.ephigenia.de</a>',
			),
			array(
				'www.ephigenia.de',
				'www.ephigenia.de',
			),
		);
	}
	
	/**
	 * @dataProvider testAutoURLEqualValues
	 */
	public function testAutoURL($input, $expectedResult)
	{
		$this->assertEquals($this->fixture->autoURL($input), $expectedResult);
	}
	
	public function testAutoURLWithAttributes()
	{
		$this->assertEquals(
			$this->fixture->autoURL('visit me on http://www.ephigenia.de/', 'class="selected"'),
			'visit me on <a href="http://www.ephigenia.de/" class="selected">http://www.ephigenia.de/</a>'
		);
	}
	
	public function autoEmailEqualValues()
	{
		return array(
			array(
				'love@ephigenia.de',
				'<a href="mailto:love@ephigenia.de">love@ephigenia.de</a>',
			),
			array(
				'my email addy is: love@ephigenia.de',
				'my email addy is: <a href="mailto:love@ephigenia.de">love@ephigenia.de</a>'
			),
			array(
				'Marcel Eichner (love@ephigenia.de)',
				'Marcel Eichner (<a href="mailto:love@ephigenia.de">love@ephigenia.de</a>)',
			),
			array(
				'Please contact me at: love@ephigenia.de.',
				'Please contact me at: <a href="mailto:love@ephigenia.de">love@ephigenia.de</a>.',
			),
			array(
				'Baltasar X M (xm.baltasa_r@fakehost.dot.eu)',
				'Baltasar X M (<a href="mailto:xm.baltasa_r@fakehost.dot.eu">xm.baltasa_r@fakehost.dot.eu</a>)',
			),
		);
	}
	
	/**
	 * @dataProvider autoEmailEqualValues
	 */
	public function testAutoEmail($input, $expectedResult)
	{
		$this->assertEquals($this->fixture->autoEmail($input), $expectedResult);
	}
	
	public function testAutoEmailWithAttributes()
	{
		$this->assertEquals(
			$this->fixture->autoEmail('love@ephigenia.de', 'class="email"'),
			'<a href="mailto:love@ephigenia.de" class="email">love@ephigenia.de</a>'
		);
	}
}