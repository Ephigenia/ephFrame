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
require_once dirname(__FILE__).'/../autorun.php';

/**
 * [SOME DOCU HERE WOULD BE NICE HEE!?]
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class testCSV extends UnitTestCase
{
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.CSV');
	}
	
	public function testCreate() {
		$csv = new CSV(array('test' => 'test'));
	}
	
	public function testRead() {
		$csv = new CSV(dirname(__FILE__).'/../tmp/csvfile.csv');
		while($data = $csv->read()) {
			$this->assertTrue(is_array($data));
		}
	}
	
	public function testFromArray() {
		$csv = new CSV(array(array('1', 2, 'test', 'CSV rendering', '"Party hard')));
		$this->assertEqual($csv->render(), '"1";"2";"test";"CSV rendering";"""Party hard"');
	}
	
	public function testRender() {
		$csv = new CSV();
		$csv->add(array('1', 2, 'test', 'CSV rendering', '"Party hard'));
		$this->assertEqual($csv->render(), '"1";"2";"test";"CSV rendering";"""Party hard"');
	}
	
	
}
