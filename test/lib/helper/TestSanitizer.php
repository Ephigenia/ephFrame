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
 * This class is part of ephFrame test with simpletests and will test
 * the {@link Sanitizr}.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestSanitizer extends UnitTestCase 
{
	public function setUp() 
	{
		ephFrame::loadClass('ephFrame.lib.helper.Sanitizer');
	}
	
	public function testFilename() 
	{
		$filenames = array(
			'simplename.txt'	=> 'simplename.txt',
			'../simple.txt'		=> 'simple.txt',
			'../../test. ext' => 'test._ext',
			'../../test.üext' => 'test.ueext'
		);
		foreach($filenames as $test => $result) {
			$this->assertEqual(Sanitizer::filename($test), $result);
		}
		exit;
	}	
}
