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
class TestCharset extends UnitTestCase
{
	public function testIsASCII() 
	{
		$this->assertTrue(Charset::isASCII('abcdefghijklmop'));
		$this->assertFalse(Charset::isASCII(chr(123)));
	}
	
	public function testIsUTF8() 
	{
		$this->assertTrue(Charset::isUTF8('bä'));
	}
	
	public function testToSingleBytes() 
	{
		$this->assertEqual(Charset::toSingleBytes('Bär'), 'Baer');
		$this->assertEqual(Charset::toSingleBytes('Ègalité'), 'Egalite');
		$this->assertEqual(Charset::toSingleBytes('ÄÜÖß'), 'AEUEOEss');
	}	
}