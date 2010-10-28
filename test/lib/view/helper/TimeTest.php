<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
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
 * Test for the TimeHelper
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestTime extends UnitTestCase 
{
	public function setUp() 
	{
		Library::load('ephFrame.lib.view.helper.Time');
	}
	
	public function testNiceShort() 
	{
		$time1 = mktime(0, 0, 0, 6, 1, 2009);
		foreach(array(
			mktime(0, 0, 0, 6, 1, 2009) => 'jetzt',
			mktime(0, 0, 10, 6, 1, 2009) => '10 Sekunden',
			mktime(0, 0, 59, 6, 1, 2009) => '59 Sekunden',
			mktime(0, 0, 29, 6, 1, 2009) => '29 Sekunden',
			mktime(0, 0, 30, 6, 1, 2009) => '30 Sekunden',
			mktime(0, 0, 31, 6, 1, 2009) => '31 Sekunden',
			mktime(12, 0, 0, 6, 1, 2009) => '12 Stunden',
			mktime(0, 1, 0, 6, 1, 2009) => '1 Minute',
			mktime(0, -1, 0, 6, 1, 2009) => '1 Minute',
			mktime(0, 59, 59, 6, 1, 2009) => '60 Minuten',
			mktime(0, -1, 0, 6, 1, 2008) => '1 Jahr',
			mktime(0, 0, 0, 3, 1, 2009) => '3 Monaten',
			) as $left => $right) {
			$this->assertEqual(Time::niceShort($time1, $left), $right);
		}
	}
	
	public function testNice() 
	{
		// 2009-10-10 12:00:00
		$time = mktime(12, 0, 0, 10, 10, 2009);
		foreach(array(
			mktime(12, 0, 0, 10, 10, 2009) => 'jetzt',
			mktime(12, 0, 29, 10, 10, 2009) => '29 Sekunden',
			mktime(13, 0, 29, 10, 10, 2009) => '1 Stunde, 29 Sekunden',
			mktime(13, 0, 30, 10, 10, 2009) => '1 Stunde, 30 Sekunden',
			mktime(13, 0, 40, 10, 10, 2009) => '1 Stunde, 40 Sekunden',
			mktime(12, 0, 0, 11, 10, 2009) => '1 Monat, 1 Tag',
			mktime(12, 1, 0, 11, 10, 2009) => '1 Monat, 1 Tag',
			mktime(12, 1, 0, 10, 10, 2009) => '1 Minute',
			mktime(12, 1, 1, 10, 10, 2009) => '1 Minute, 1 Sekunde',
			mktime(12, 1, 31, 10, 10, 2009) => '1 Minute, 31 Sekunden',
			) as $left => $right) {
			$this->assertEqual(Time::nice($time, $left), $right);
		}
	}
	
	public function testIsToday() 
	{
		$this->assertEqual(Time::isToday(time()), true);
		$this->assertEqual(Time::isToday(time()-DAY), false);
	}
	
	public function testIsYesterday() 
	{
		$this->assertEqual(Time::isYesterday(time()), false);
		$this->assertEqual(Time::isYesterday(time()-DAY), true);
	}
}