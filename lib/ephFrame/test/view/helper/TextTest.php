<?php

namespace ephFrame\test\view\helper;

use ephFrame\view\helper\Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Text();
	}
	
	public function testMore()
	{
		$text = 'Lorem Ipsum <!--more--> doloret';
		$url = '#';
		$this->assertEquals($this->fixture->more($text, $url),
			'Lorem Ipsum <a href="#">more</a>'
		);
		$text = 'Lorem Ipsum <!--more--> doloret';
		$url = '#';
		$this->assertEquals($this->fixture->more($text, $url, 'alternate'),
			'Lorem Ipsum <a title="alternate" href="#">alternate</a>'
		);
		
		$text = 'Lorem Ipsum <!--Read more after the click--> doloret';
		$this->assertEquals($this->fixture->more($text, $url),
			'Lorem Ipsum <a href="#">Read more after the click</a>'
		);
		
		$text = 'Lorem Ipsum <!--This is kind-of tricky>>--> doloret';
		$this->assertEquals($this->fixture->more($text, $url),
			'Lorem Ipsum <a href="#">This is kind-of tricky&gt;&gt;</a>'
		);
		
		$text = 'Lorem Ipsum <!-- Read & Learn…--> doloret';
		$this->assertEquals($this->fixture->more($text, $url),
			'Lorem Ipsum <a href="#"> Read &amp; Learn…</a>'
		);
		
		$text = 'Lorem Ipsum <!--morelabel--> doloret';
		$this->assertEquals($this->fixture->more($text, $url, $defaultLabel = 'go on reading…'),
			'Lorem Ipsum <a title="go on reading…" href="#">morelabel</a>'
		);
		
		// test not - escaping of label
		$text = 'Lorem Ipsum <!--go on <em>reading</em>--> doloret';
		$this->assertEquals($this->fixture->more($text, $url, false, array('escaped' => false)),
			'Lorem Ipsum <a href="#">go on <em>reading</em></a>'
		);
	}
	
	public function testMoreWithAttributes()
	{
		$text = 'Lorem Ipsum <!--morelabel--> doloret';
		$url = '#';
		$attributes = array('class' => 'morelink');
		$this->assertEquals($this->fixture->more($text, $url, $defaultLabel = 'go on reading…', $attributes),
			'Lorem Ipsum <a class="morelink" title="go on reading…" href="#">morelabel</a>'
		);
		
		$attributes['title'] = 'my new title';
		$this->assertEquals($this->fixture->more($text, $url, $defaultLabel = 'go on reading…', $attributes),
			'Lorem Ipsum <a class="morelink" title="my new title" href="#">morelabel</a>'
		);
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
	public function testSimpleAutoURL($input, $expectedResult)
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
	
	public function testExcerptValues() 
	{
		$text = 'Hello My Name is charly. I’m your Father!   '.PHP_EOL.'What do you think?';
		return array(
			array($text, 1, 'Hello My Name is charly.'),
			array($text, 2, 'Hello My Name is charly. I’m your Father!'),
			array($text, 3, $text),
			array($text, 5, $text),
		);
	}
	
	/**
	 * @dataProvider testExcerptValues()
	 */
	public function testExcerpt($text, $count, $result)
	{
		$this->assertEquals($this->fixture->excerpt($text, $count), $result);
	}
	
	public function testCountParagraphsValues()
	{
		return array(
			array('', 0),
			array('1'.PHP_EOL, 1),
			array('1'.PHP_EOL.PHP_EOL, 2),
		);
	}
	
	/**
	 * @dataProvider testCountParagraphsValues()
	 */
	public function testCountParagraphs($string, $result)
	{
		$this->assertEquals($this->fixture->countParagraphs($string), $result);
	}
}