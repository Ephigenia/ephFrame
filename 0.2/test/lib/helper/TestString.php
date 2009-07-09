<?php

/**
 * ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright	copyright 2007+, Ephigenia M. Eichner
 * @link		http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
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
class TestString extends UnitTestCase {
	
	public function testAppend() {
		$this->assertEqual(String::append('A', 'B'), 'AB');
	}
	
	public function testPrepend() {
		$this->assertEqual(String::prepend('B', 'A'), 'AB');
	}

	public function testLeft() {
		$this->assertEqual(String::left('Mähdrescher', 3), 'Mäh');
	}
		
	public function testRight() {
		$this->assertEqual(String::right('Scheiß', 2), 'iß');
		$this->assertEqual(String::right('Scheiß', 2), 'iß');
	}
	
	public function testToURL() {
		$this->assertEqual(String::toURL('bätmän'), 'baetmaen');
		$this->assertEqual(String::toURL('bätmän\'s mobile is cool'), 'baetmaens-mobile-is-cool');
		$this->assertEqual(String::toURL('bätmän\'s mobile is cool', '_'), 'baetmaens_mobile_is_cool');
		$this->assertEqual(String::toURL('bätmän\'s mobile is cool', ''), 'baetmaensmobileiscool');
		$this->assertEqual(String::toURL('bätmän\'s mobile'.LF.' is cool', '_'), 'baetmaens_mobile_is_cool');
	}
	
	public function testUpper() {
		$this->assertEqual(String::upper('Mähdrescher'), 'MÄHDRESCHER');
	}

	public function testUcFirst() {
		$this->assertEqual(String::ucFirst('östlich'), 'Östlich');
	}

	public function testLower() {
		$this->assertEqual(String::lower('MÄHDRESCHER'), 'mähdrescher');
	}
	
	public function testLcFirst() {
		$this->assertEqual(String::lcFirst('Ähdrescher'), 'ähdrescher');
	}
	
	public function testSubstitute() {
		$expectedResult = 'You\'re seeing page 1 of 2 pages in total';
		$expectedResultMultiple = '1 of 2 pages, you are on page 1';
		// with associative arrays
		$this->assertEqual(String::substitute('You\'re seeing page %page% of %total% pages in total',
			array(
				'page' => 1,
				'total' => 2	
			)
		), $expectedResult);
		// associative array multiple
		// with associative arrays
		$this->assertEqual(String::substitute('%page% of %total% pages, you are on page %page%',
			array(
				'page' => 1,
				'total' => 2	
			)
		), $expectedResultMultiple);
		// with indexed array
		$this->assertEqual(
			String::substitute('You\'re seeing page %1% of %2% pages in total', 1, 2),
			$expectedResult);
		// with indexed array, multiple usage
		$this->assertEqual(
			String::substitute('%1% of %2% pages, you are on page %1%', 1, 2),
			$expectedResultMultiple);
		// indexed / associative array
		// not supported now
	}
	
	public function testLength() {
		$this->assertEqual(String::length('Waschbär'), 8);
		$this->assertEqual(String::length('Wa    är'), 8);
		$this->assertEqual(String::length('”'), 1);
	}
	
	public function testSubstr() {
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
	/*
	public function testCountWords() {
		$this->assertEqual(String::countWords('Hallo'), array('Hallo' => 1));
	}
	*/
	
	// todo add all multiline power here
	public function testIndent() {
		// simple indenting
		$this->assertEqual(String::indent('indent me!'), "\tindent me!");
	}
	
	public function testInsert() {
		$this->assertEqual(String::insert('ADEFG', 1, 'BC'), 'ABCDEFG');
		$this->assertEqual(String::insert('ADEFG', -4, 'BC'), 'ABCDEFG');
	}
	
	public function testEachLine() {
		$this->assertEqual(String::eachLine('Test'.LF.LF.'One'), array('Test', '', 'One'));
		$this->assertEqual(String::eachLine('Test'.LF.LF.'One', true), array('Test', 'One'));
	}
	
	public function test() {
		$result = String::substitute('You\'re seeing page %page% of %total% pages', array(
			'total' => 100,
			'page' => 1
		));
		$this->assertEqual($result, 'You\'re seeing page 1 of 100 pages');
		$result = String::substitute('You\'re seeing page %1% of %0% pages', array(
			100,
			1
		));
		$this->assertEqual($result, 'You\'re seeing page 1 of 100 pages');
	}
	
	public function testEach() {
		// test simple each
		$this->assertEqual(String::each('Test'), array('T', 'e', 's', 't'));
		// test multibyte
		$this->assertEqual(String::each('ÄBÖ'), array('Ä', 'B', 'Ö'));
	}
	
	public function testAddLineNumbers() {
		$text = 'HALLO'.LF.'MY name is'.LF.'earl';
		$this->assertEqual(String::addLineNumbers($text), '1 HALLO'.LF.'2 MY name is'.LF.'3 earl');
	}

	/*
	public function testHex() {
		// simple testing
		$this->assertEqual(String::hex('A'), '41');
		$this->assertEqual(String::hex('AB'), '4142');
		$this->assertEqual(String::hex(chr(1)), '01');
		// testing not-usage of spacer at the end, and spacer at all
		$this->assertEqual(String::hex(chr(1).' ', ' '), '01 20');
		// testing line break
		$this->assertEqual(String::hex('ABCDEFG', 'X', 3), '01'.LF.'42');
	}
	*/
}