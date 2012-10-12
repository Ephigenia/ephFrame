<?php

namespace ephFrame\test\Filter;

use ephFrame\Filter\StripTags;

/**
 * @group Filter
 */
class StripTagsTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new StripTags();
	}
	
	public function testSimpleValues()
	{
		return array(
			array('<tag>', ''),
			array('<tag><img src="http://www.ephigenia.de" /></tag>', ''),
			array('<tag><img src="http://www.ephigenia.de"></tag>', ''),
			array('<!--comment-->', ''),
			array('<em>highlight</em>', 'highlight'),
			array('<em class="test me">highlight</em>', 'highlight'),
			array('<<em class="test me">double</em>', ''),
			array('Oiltanking & Something else', 'Oiltanking & Something else'),
			// array('<no entity at all', 'no html entity at all'), // @todo make this work!
		);
	}
	
	/**
	 * @dataProvider testSimpleValues()
	 */
	public function testSimple($left, $right)
	{
		$this->assertEquals($right, $this->fixture->apply($left));
	}
	
	public function testXSSValues()
	{
		return array(
			array('<<SCRIPT>alert("XSS");//<</SCRIPT>', ''),
			array('<SCRIPT>alert("XSS");//</SCRIPT>', 'alert("XSS");//'),
			array('<BODY BACKGROUND="javascript:alert(\'XSS\')">', ''),
			array('<BODY BACKGROUND="javascript:alert(\'XSS\')" />', ''),
			array('%3Cbold&#0*62;test', 'test'),
			array('</TITLE><SCRIPT>alert("XSS");</SCRIPT>', 'alert("XSS");'),
			array('<SCRIPT =">" SRC="http://ha.ckers.org/xss.js"></SCRIPT>', ''),
		);
	}
	
	public function testAllowedTagsValues()
	{
		return array(
			array('<b><strong>bold</strong></b>', array('b'), '<b>bold</b>'),
			array('<b class="test"><strong>bold</strong></b>', array('b'), '<b class="test">bold</b>'),
			array('<b class="test"><strong>bold</strong></b>', array('b', 'strong'), '<b class="test"><strong>bold</strong></b>'),
			array('<b>bold</b>', '<b>', '<b>bold</b>'),
			array('line<br />break', '<br>', 'line<br />break'),
		);
	}
	
	/**
	 * @dataProvider testAllowedTagsValues()
	 */
	public function testAllowedTags($left, $tags, $right) 
	{
		$this->fixture->allowed = $tags;
		$this->assertEquals($right, $this->fixture->apply($left));
	}
	
	/**
	 * @dataProvider testXSSValues()
	 */
	public function testXSS($left, $right)
	{
		$this->assertEquals($right, $this->fixture->apply($left));
	}
	
	public function testNotClosedHTMLTagValues()
	{
		return array(
			array('<em class="attribute value">Check Test', 'Check Test'),
			array('<em class="attribute value">Check Test</b>', 'Check Test'),
		);
	}
	
	/**
	 * @dataProvider testNotClosedHTMLTagValues()
	 */
	public function testNotClosedHTMLTag($left, $right)
	{
		$this->assertEquals($right, $this->fixture->apply($left));
	}
}