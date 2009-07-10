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
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version     $Revision$
 * @filesource
 */

// init simpletest and framework
require_once dirname(__FILE__).'/../autorun.php';

/**
 * [SOME DOCU HERE WOULD BE NICE HEE!?]
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 06.10.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestInflector extends UnitTestCase {
	
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.Inflector');
	}
	
	public function testPluralize() {
		$tests = array(
			'User'  => 'Users',
			'user'	=> 'users',
			'Country'	=> 'Countries',
			'boy' => 'boys',
			'girl' => 'girls',
			// -ies rule
			'cherry' => 'cherries',
			'lady'	=> 'ladies',
			// -oes rule,
			'hero' => 'heroes', 'potato' => 'potatoes', 'volcano' => 'volcanoes'
		);
		foreach($tests as $left => $right) {
			$this->assertEqual(Inflector::pluralize($left), $right);
		}
	}
	
	public function testCamellize() {
		$this->assertEqual(Inflector::camellize('my_class_name'), 'myClassName');
		$this->assertEqual(Inflector::camellize('my class name'), 'myClassName');
		$this->assertEqual(Inflector::camellize('my class näme'), 'myClassNäme');
		$this->assertEqual(Inflector::camellize('my class näme', true), 'MyClassNäme');
		$this->assertEqual(Inflector::camellize('my'.LF.' class näme'), 'myClassNäme');
	}
	
	public function testDelimeterSeperate() {
		$a = array(
			'hallo my name is Earl' => 'hallo_my_name_is_earl',
			'youAreSo Great' => 'you_are_so_great',
			'testTHe_great' => 'test_t_he_great',
			'  master Testa' => 'master_testa',
			' @freak 123   ' => '@freak_123'
		);
		foreach($a as $input => $output) {
			$this->assertEqual(Inflector::delimeterSeperate($input), $output);
		}
		$this->assertEqual(Inflector::delimeterSeperate(' space is good'.LF, '-'), 'space-is-good');
	}
	
}