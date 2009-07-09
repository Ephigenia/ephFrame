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
 **/

// init simpletest and framework
require_once dirname(__FILE__).'/../autorun.php';

/**
 * SimpleTest Class testing the {@link Hash} class from the ephFrame Framework
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestHash extends UnitTestCase
{	
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.Hash');
	}
	
	public function testSort() {
		$test = new Hash();
		$test->append('Name', 'Hossa');
		$test->sort();
		$this->assertEqual($test->implode(), 'HossaName');
	}
	
	public function testAdd() {
		$r = new Hash();
		$r->add('test');
		$this->assertEqual($r->toArray(), array(0 => 'test'));
	}
	
	public function testToString() {
		$test = new Hash();
		$test->append('Name', 'Hossa');
		$this->assertEqual((string) $test, 'NameHossa');
	}
	
}