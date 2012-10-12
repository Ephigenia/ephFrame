<?php

namespace ephFrame\test\Filter;

use ephFrame\Filter\Alpha;

/**
 * @group Filter
 */
class AlphaTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Alpha(array('unicode' => false, 'whitespace' => false));
	}
	
	public function testConstructor()
	{
		$this->assertFalse($this->fixture->unicode);
		$this->assertFalse($this->fixture->whitespace);
	}
	
	public function testInvoke()
	{
		$r = $this->fixture;
		$this->assertEquals('abc', $r('1abc3'));
	}
	
	public function testSimpleValues()
	{
		return array(
			array(false, ''),
			array(123, ''),
			array(0x0, ''),
			array(0xFE, ''),
			array('abc', 'abc'),
			array(' abc', 'abc'),
			array("\t_d. a", 'da'),
			array("\n'*\\alöffel", 'alffel'),
			array('.#+ä', ''),
		);
	}
	
	/**
	 * @dataProvider testSimpleValues()
	 */
	public function testSimple($input, $output)
	{
		$this->assertEquals($output, $this->fixture->apply($input));
	}
	
	public function testUnicodeValues()
	{
		return array(
			array('ABC', 'ABC'),
			array('ÄBÖ', 'ÄBÖ'),
			array("\rÉ€®", 'É'),
			array('    _#+', ''),
		);
	}
	
	/**
	 * @dataProvider testUnicodeValues()
	 */
	public function testUnicode($input, $output)
	{
		$this->fixture->unicode = true;
		$this->assertEquals($output, $this->fixture->apply($input));
	}
	
	public function testUnicodeWhiteSpaceValues()
	{
		return array(
			array("Hello\rThis is my Message text", "Hello\rThis is my Message text"),
			array("\rÉ€®", "\rÉ"),
			array('    _#+]', '    '),
		);
	}
	
	/**
	 * @dataProvider testUnicodeWhiteSpaceValues()
	 */
	public function testUnicodeWhiteSpace($input, $output)
	{
		$this->fixture->unicode = $this->fixture->whitespace = true;
		$this->assertEquals($output, $this->fixture->apply($input));
	}
	
	public function testAdditionalCharsValues()
	{
		return array(
			array('ABC', 'ABC'),
			array('ÄBÖ', 'ÄBÖ'),
			array("\rÉ€®", 'É€®'),
			array('    _#+]', '#]'),
		);
	}
	
	/**
	 * @dataProvider testAdditionalCharsValues()
	 */
	public function testAdditionalChars($input, $output)
	{
		$this->fixture->unicode = true;
		$this->fixture->whitespace = false;
		$this->fixture->chars = array('€', '®', '#', ']');
		$this->assertEquals($output, $this->fixture->apply($input));
	}	
}