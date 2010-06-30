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
 * Test for {@link Timer} Helper class
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2010-03-12
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestTimer extends UnitTestCase
{
	public function setUp() 
	{
		ephFrame::loadClass('ephFrame.lib.helper.Timer');
	}
	
	public function testSingleTimer()
	{
		$timer = new Timer();
		sleep(0.2);
		echo $timer;
		exit;
	}
}