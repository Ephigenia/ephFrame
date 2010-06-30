<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright	copyright 2007+, Ephigenia M. Eichner
 * @link		http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

// init simpletest and framework
require_once dirname(__FILE__).'/../../autorun.php';

/**
 * [SOME DOCU HERE WOULD BE NICE HEE!?]
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestString extends UnitTestCase 
{
	public function testAppend() 
	{
		$this->assertEqual(String::append('A', 'B'), 'AB');
		$this->assertEqual(String::append('AC', 'C', true), 'AC');
		$this->assertEqual(String::append('AC', 'c', true, true), 'ACc');
		$this->assertEqual(String::append('AC', 'c', true, false), 'AC');
		$this->assertEqual(String::append('', ''), '');
		$this->assertEqual(String::append('', '', true, true), '');
	}
	
	public function testPrepend() 
	{
		$this->assertEqual(String::prepend('www.ephigenia.de', 'http://'), 'http://www.ephigenia.de');
		$this->assertEqual(String::prepend('http://www.ephigenia.de', 'http://', true), 'http://www.ephigenia.de');
		$this->assertEqual(String::prepend('B', 'A'), 'AB');
	}

	public function testLeft() 
	{
		$this->assertEqual(String::left('Mähdrescher', 3), 'Mäh');
	}
		
	public function testRight() 
	{
		$this->assertEqual(String::right('Scheiß', 2), 'iß');
	}
	
	public function testToURL() 
	{
		$this->assertEqual(String::toURL('bätmän'), 'baetmaen');
		$this->assertEqual(String::toURL('bätmän\'s mobile is cool'), 'baetmaens-mobile-is-cool');
		$this->assertEqual(String::toURL('bätmän\'s mobile is __cool', '_'), 'baetmaens_mobile_is_cool');
		$this->assertEqual(String::toURL('bätmän\'s mobile is  cool', ''), 'baetmaensmobileiscool');
		$this->assertEqual(String::toURL('bätmän\'s mobile'.LF.' is cool', '_'), 'baetmaens_mobile_is_cool');
	}
	
	public function testUpper() 
	{
		$this->assertEqual(String::upper('Mähdrescher'), 'MÄHDRESCHER');
	}

	public function testUcFirst() 
	{
		$this->assertEqual(String::ucFirst('östlich'), 'Östlich');
	}

	public function testLower() 
	{
		$this->assertEqual(String::lower('MÄHDRESCHER'), 'mähdrescher');
	}
	
	public function testLcFirst() 
	{
		$this->assertEqual(String::lcFirst('Ähdrescher'), 'ähdrescher');
	}
	
	public function testTruncate()
	{
		$tests = array(
			// html closing
			array(
				array('ABC<i>DEF</i>G', 4),
				'ABC',
			),
			array(
				array('ABC<strong>DEF</strong>G', 7),
				'ABC<strong>DEF</strong>',
			),
			array(
				array('ABC<i>DEF</i>G', 7),
				'ABC<i>DEF</i>',
			),
			// other simpler rules
			array(
				array('ABC DEFGHIJKLMNOP', 4, '-'),
				'ABC-',
			),
			array(
				array('ABC DEFGHIJKLMNOP', 5, '-'),
				'ABC-',
			),
			array(
				array('ABC D EFGHIJKLMNOP', 6),
				'ABC D',
			),
			array(
				array('ABC D EFGHIJKLMNOP', 7, '-'),
				'ABC D-',
			),
			array(
				array('ABCDEFGHIJKLMNOP', 3),
				'ABC',
			),
			array(
				array('ABCDEFGHIJKLMNOP', 4, '-'),
				'ABC-',
			),
			array(
				array('ABCDEFGHIJKLMNOP', 4, '--'),
				'AB--',
			),
		);
		foreach($tests as $test) {
			$this->assertEqual(call_user_func_array('String::truncate', $test[0]), $test[1]);
		}
	}
	
	public function testCloseTags()
	{
		$tests = array(
			array(
				array('This is a test'),
				'This is a test',
			),
			// short tags
			array(
				array('This <i>is a test'),
				'This <i>is a test</i>',
			),
			array(
				array('This <i>is'),
				'This <i>is</i>',
			),
			// longer tags
			array(
				array('This <strong>Bold'),
				'This <strong>Bold</strong>',
			),
			// tags with attributes
			array(
				array('This <a href="http://www.marceleichner.de" target="_blank">Bold'),
				'This <a href="http://www.marceleichner.de" target="_blank">Bold</a>',
			),
			// encapsulated tags
			array(
				array('This <strong><a href="http://www.marceleichner.de" target="_blank">Bold</strong>'),
				'This <strong><a href="http://www.marceleichner.de" target="_blank">Bold</strong></a>',
			),
		);
		foreach($tests as $test) {
			$this->assertEqual(call_user_func_array('String::closeTags', $test[0]), $test[1]);
		}
	}
	
	public function testSubstitute() 
	{
		$expectedResult = 'You\'re seeing page 1 of 2 pages in total';
		$expectedResultMultiple = '1 of 2 pages, you are on page 1';
		// with associative arrays
		$this->assertEqual(String::substitute('You\'re seeing page :page of :total pages in total',
			array(
				'page' => 1,
				'total' => 2	
			)
		), $expectedResult);
		// associative array multiple
		// with associative arrays
		$this->assertEqual(String::substitute(':page of :total pages, you are on page :page',
			array(
				'page' => 1,
				'total' => 2	
			)
		), $expectedResultMultiple);
		// with indexed array
		$this->assertEqual(
			String::substitute('You\'re seeing page :1 of :2 pages in total', 1, 2),
			$expectedResult);
		// with indexed array, multiple usage
		$this->assertEqual(
			String::substitute(':1 of :2 pages, you are on page :1', 1, 2),
			$expectedResultMultiple);
		// indexed / associative array
		// not supported now
	}
	
	public function testLength() 
	{
		$this->assertEqual(String::length('Waschbär'), 8);
		$this->assertEqual(String::length('Wa    är'), 8);
		$this->assertEqual(String::length('”'), 1);
	}
	
	public function testSubstr() 
	{
		$t = 'Waschbär';
		$this->assertEqual(String::substr($t, 0), $t);
		$this->assertEqual(String::substr($t, 1), 'aschbär');
		$this->assertEqual(String::substr($t, 6), 'är');
		$this->assertEqual(String::substr($t, 0, 1), 'W');
		$this->assertEqual(String::substr($t, 6, 1), 'ä');
		$this->assertEqual(String::substr($t, -2, 1), 'ä');
		$this->assertEqual(String::substr($t, -2), 'är');
		$this->assertEqual(String::substr($t, -2, 0), '');
		$this->assertEqual(String::substr($t, 0, 0), '');
		$this->assertEqual(String::substr($t, 1, 0), '');
	}
	
	// todo add all multiline power here
	public function testIndent() 
	{
		// simple indenting
		$this->assertEqual(String::indent('indent me!'), "\tindent me!");
	}
	
	public function testInsert() 
	{
		$this->assertEqual(String::insert('ADEFG', 1, 'BC'), 'ABCDEFG');
		$this->assertEqual(String::insert('ADEFG', -4, 'BC'), 'ABCDEFG');
	}
	
	public function testEachLine() 
	{
		$this->assertEqual(String::eachLine('Test'.LF.LF.'One'), array('Test', '', 'One'));
		$this->assertEqual(String::eachLine('Test'.LF.LF.'One', true), array('Test', 'One'));
	}
	
	public function test() 
	{
		$result = String::substitute('You\'re seeing page :page of :total pages', array(
			'total' => 100,
			'page' => 1
		));
		$this->assertEqual($result, 'You\'re seeing page 1 of 100 pages');
		$result = String::substitute('You\'re seeing page :1 of :0 pages', array(
			100,
			1
		));
		$this->assertEqual($result, 'You\'re seeing page 1 of 100 pages');
	}
	
	public function testEach()
	{
		// test simple each
		$this->assertEqual(String::each('Test'), array('T', 'e', 's', 't'));
		// test multibyte
		$this->assertEqual(String::each('ÄBÖ'), array('Ä', 'B', 'Ö'));
	}
	
	public function testAddLineNumbers() 
	{
		$text = 'HALLO'.LF.'MY name is'.LF.'earl';
		$this->assertEqual(String::addLineNumbers($text), '1 HALLO'.LF.'2 MY name is'.LF.'3 earl');
	}

	public function testHex() 
	{
		// simple testing
		$this->assertEqual(String::hex('A'), '41');
		$this->assertEqual(String::hex('AB'), '4142');
		$this->assertEqual(String::hex(chr(1)), '01');
		// testing not-usage of spacer at the end, and spacer at all
		$this->assertEqual(String::hex('AB', ' '), '41 42');
		$this->assertEqual(String::hex('AB', 'X'), '41X42');
		$this->assertEqual(String::hex('ABC', 'X'), '41X42X43');
	}
}