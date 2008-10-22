<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright 2007+, Ephigenia M. Eichner, Kopernikusstr. 8, 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 	@license http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright Copyright 2007+, Ephigenia M. Eichner
 * 	@link http://code.ephigenia.de/projects/ephFrame/
 * 	@filesource
 */

// init simpletest and framework
require_once dirname(__FILE__).'/../../autorun.php';

/**
 * 	[SOME DOCU HERE WOULD BE NICE HEE!?]
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 18.08.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.test
 */
class TestTime extends UnitTestCase {
	
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.helper.Time');
	}
	
	public function testIsToday() {
		$this->assertEqual(Time::isToday(time()), true);
		$this->assertEqual(Time::isToday(time()-DAY), false);
	}
	
	public function testIsYesterday() {
		$this->assertEqual(Time::isYesterday(time()), false);
		$this->assertEqual(Time::isYesterday(time()-DAY), true);
	}
	
}

?>