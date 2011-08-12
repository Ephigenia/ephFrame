<?php

namespace ephFrame\test\util;

use ephFrame\util\String;

/**
 * @group Util
 */
class StringTest extends \PHPUnit_Framework_TestCase
{
	public function testLeft() 
	{
		$this->assertEquals(String::left('Mähdrescher', 3), 'Mäh');
		$this->assertEquals(String::left('Mähdrescher', -1), '');
	}
		
	public function testRight() 
	{
		$this->assertEquals(String::right('Scheiß', 2), 'iß');
		$this->assertEquals(String::right('Scheiß', -2), '');
	}
	
	public function testUpper() 
	{
		$this->assertEquals(String::upper('Mähdrescher'), 'MÄHDRESCHER');
	}

	public function testUcFirst() 
	{
		$this->assertEquals(String::ucFirst('östlich'), 'Östlich');
	}

	public function testLower() 
	{
		$this->assertEquals(String::lower('MÄHDRESCHER'), 'mähdrescher');
	}
	
	public function testLcFirst() 
	{
		$this->assertEquals(String::lcFirst('Ähdrescher'), 'ähdrescher');
	}
	
	public function testSubstituteEmptyTemplate()
	{
		$this->assertEquals(String::substitute('This is a string without template', array('aslkdj')), 'This is a string without template');
	}
	
	public function testSubstituteSingleVar()
	{
		$this->assertEquals(String::substitute('My name is: :0', 'Karl'), 'My name is: Karl');
	}
	
	public function testSubstitute() 
	{
		$expectedResult = 'You’re seeing page 1 of 2 pages in total';
		$expectedResultMultiple = '1 of 2 pages, you are on page 1';
		// with associative arrays
		$this->assertEquals(String::substitute('You’re seeing page :page of :total pages in total',
			array(
				'page' => 1,
				'total' => 2	
			)
		), $expectedResult);
		// associative array multiple
		// with associative arrays
		$this->assertEquals(String::substitute(':page of :total pages, you are on page :page',
			array(
				'page' => 1,
				'total' => 2	
			)
		), $expectedResultMultiple);
		// with indexed array
		$this->assertEquals(
			String::substitute('You’re seeing page :1 of :2 pages in total', 1, 2),
			$expectedResult);
		// with indexed array, multiple usage
		$this->assertEquals(
			String::substitute(':1 of :2 pages, you are on page :1', 1, 2),
			$expectedResultMultiple);
		// more complex placeholders
		$this->assertEquals(
			String::substitute(':action_:id/:param-name-token', array(
				'action' => 'register',
				'id' => 1,
				'param-name' => 'super',
			)),
			'register_1/super-token'
		);
	}
	
	public function testSubstituteSimilarKeys()
	{
		$this->assertEquals(
			String::substitute('/:ids/:id', array(
				'id' => 1,
				'ids' => '1,2,3',
			)),
			'/1,2,3/1'
		);
	}
	
	public function testIndent() 
	{
		$this->assertEquals(String::indent('indent me!'), "\tindent me!");
		$this->assertEquals(String::indent('A'.PHP_EOL.'B', 1, 'X'), 'XA'.PHP_EOL.'XB');
		$this->assertEquals(String::indent('A'.PHP_EOL.'B', 5, 'X'), 'XXXXXA'.PHP_EOL.'XXXXXB');
	}
	
	public function testLength() 
	{
		$this->assertEquals(String::length('Waschbär'), 8);
		$this->assertEquals(String::length('Wa    är'), 8);
		$this->assertEquals(String::length('”'), 1);
		$this->assertEquals(String::length('…'), 1);
	}
	
	public function testSubstr() 
	{
		$t = 'Waschbär';
		$this->assertEquals(String::substr($t, 0), $t);
		$this->assertEquals(String::substr($t, 1), 'aschbär');
		$this->assertEquals(String::substr($t, 6), 'är');
		$this->assertEquals(String::substr($t, 0, 1), 'W');
		$this->assertEquals(String::substr($t, 6, 1), 'ä');
		$this->assertEquals(String::substr($t, -2, 1), 'ä');
		$this->assertEquals(String::substr($t, -2), 'är');
		$this->assertEquals(String::substr($t, -2, 0), '');
		$this->assertEquals(String::substr($t, 0, 0), '');
		$this->assertEquals(String::substr($t, 1, 0), '');
	}
	
	public function testTruncateSingleLine()
	{
		$this->assertEquals(String::truncate('Lorem Ipsum doloret', 13, 'X'), 'Lorem IpsumX');
		$this->assertEquals(String::truncate('Lorem Ipsum doloret', 13, '…'), 'Lorem Ipsum…');
		$this->assertEquals(String::truncate('Lorem Ipsum doloret', 5, 'X'), 'LoreX');
		$this->assertEquals(String::truncate('Lorem Ipsum doloret', 5, '…'), 'Lore…'); // same test but with multibyte
	}
	
	public function testTruncateForce()
	{
		$this->assertEquals(String::truncate('Donausschifffahrtsamt', 10, ' …'), 'Donaussc …');
	}
	
	public function testTruncateWithHTML()
	{
		$this->assertEquals(String::truncate('<em>Lorem</em> <strong>Ipsum doloret</strong> something', 13, '…'), '<em>Lorem</em> <strong>Ipsum</strong>…');
	}
		
	public function testHex() 
	{
		// simple testing
		$this->assertEquals(String::hex('A'), '41');
		$this->assertEquals(String::hex('AB'), '4142');
		$this->assertEquals(String::hex(chr(1)), '01');
	}
	
	public function testHexWithSpace()
	{
		$this->assertEquals(String::hex('AB', ' '), '41 42');
		$this->assertEquals(String::hex('AB', 'X'), '41X42');
		$this->assertEquals(String::hex('ABC', 'X'), '41X42X43');
	}
	
	public function testHexWithBreak()
	{
		$this->assertEquals(String::hex('ABCDEF', ' ', 3), '41 42 43'.PHP_EOL.'44 45 46');
	}
	
	public function testHtmlOrdEncode()
	{
		$this->assertEquals(String::htmlOrdEncode('love@ephigenia.de'), '&#108;&#111;&#118;&#101;&#64;&#101;&#112;&#104;&#105;&#103;&#101;&#110;&#105;&#97;&#46;&#100;&#101;');
		$this->assertEquals(String::htmlOrdEncode('Märchen'), '&#77;&#228;&#114;&#99;&#104;&#101;&#110;');
	}
	
	public function testSqueeze()
	{
		$this->assertEquals(String::squeeze('aabbaaab'), 'abab');
	}
	
	public function testEach()
	{
		$this->assertEquals(String::each('ÄBC…'), array('Ä', 'B', 'C', '…'));
	}
	
	public function testInsert()
	{
		$this->assertEquals(String::insert('ADEFG', -4, 'BC'), 'ABCDEFG');
	}
}