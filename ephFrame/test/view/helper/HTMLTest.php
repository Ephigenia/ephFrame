<?php

namespace ephFrame\test\view\helper;

use ephFrame\view\helper\HTML;

class HTMLTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->HTML = new HTML();
	}
	
	public function testTag()
	{
		$tag = $this->HTML->tag('a', 'link', array(
			'class' => array('red', 'selected'),
			'href' => 'http://www.ephigenia.de',
		));
		$this->assertEquals((string) $tag, '<a class="red selected" href="http://www.ephigenia.de">link</a>');
	}

	public function test__call()
	{
		$tag = $this->HTML->a('link label', array('class' => 'selected'));
		$this->assertEquals((string) $tag, '<a class="selected">link label</a>');
	}
	
	public function testEmailEqualValues()
	{
		return array(
			array(
				array('love@ephigenia.de'),
				'<a href="mailto:love@ephigenia.de">love@ephigenia.de</a>',
			),
			array(
				array('love@ephigenia.de', 'Marcel Eichner'),
				'<a href="mailto:love@ephigenia.de" title="Marcel Eichner">Marcel Eichner</a>',
			),
			array(
				array('love@ephigenia.de', 'Marcel Eichner', array('class' => 'contact')),
				'<a class="contact" href="mailto:love@ephigenia.de" title="Marcel Eichner">Marcel Eichner</a>',
			),
			array(
				array('love@ephigenia.de', 'Marcel Eichner', array('class' => 'contact', 'title' => 'write a mail')),
				'<a class="contact" title="write a mail" href="mailto:love@ephigenia.de">Marcel Eichner</a>',
			),
		);
	}
	
	/**
	 * @dataProvider testEmailEqualValues
	 */
	public function testEmail($args, $expectedResult)
	{
		$this->assertEquals((string) call_user_func_array(array($this->HTML, 'email'), $args), $expectedResult);
	}
	
	public function testLinkEqualValues()
	{
		return array(
			array(
				array('http://www.ephigenia.de', 'ephigenia', array('rel' => 'external')),
				'<a rel="external" href="http://www.ephigenia.de" title="ephigenia">ephigenia</a>',
			),
			array(
				array('http://www.ephigenia.de', 'label'),
				'<a href="http://www.ephigenia.de" title="label">label</a>',
			),
			array(
				array('http://www.ephigenia.de', '<strong>Strong Label</strong>', array('escaped' => false)),
				'<a href="http://www.ephigenia.de" title="Strong Label"><strong>Strong Label</strong></a>',
			),
			array(
				array('http://www.ephigenia.de', '<strong>Strong Label</strong>'),
				'<a href="http://www.ephigenia.de" title="Strong Label">&lt;strong&gt;Strong Label&lt;/strong&gt;</a>',
			),
			array(
				array('http://www.ephigenia.de'),
				'<a href="http://www.ephigenia.de">http://www.ephigenia.de</a>',
			),
		);
	}
	
	/**
	 * @dataProvider testLinkEqualValues
	 */
	public function testLink($args, $expectedResult)
	{
		$this->assertEquals((string) call_user_func_array(array($this->HTML, 'link'), $args), $expectedResult);
	}
}