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
require_once dirname(__FILE__).'/../autorun.php';

/**
 * 	Unit test for {@link Set}
 * 
 * 	This is a UnitTest Class using the Simpletest framework for acid testing the
 * 	{@link Set} class from the ephFrame framework.
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 18.08.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.test
 * 	@uses Set
 */
class TestSet extends UnitTestCase {
	
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.Set');
	}
	
	public function testImplode() {
		$test = new Set();
		$test->append('Name', 'Hossa');
		$this->assertEqual($test->implode(''), 'NameHossa');
		$this->assertEqual($test->implode(), 'NameHossa');
		$this->assertEqual($test->implode(','), 'Name,Hossa');
		$this->assertEqual($test->sort()->implode(','), 'Hossa,Name');
	}
	
	public function testFromString() {
		$t = new Set();
		$t->fromString('AABC');
		$this->assertEqual($t->toArray(), array('A', 'A', 'B', 'C'));
	}
	
	public function testUnique() {
		$t = new Set();
		$t->fromString('AABC');
		$this->assertEqual((string) $t->unique(), 'ABC');
	}
	
	public function testMinimum() {
		$t = new Set(1,2,2,3,4,5,5,1);
		$this->assertEqual($t->min(), '1');
		$this->assertEqual($t->min(0), null);
		$this->assertEqual($t->min(2), new Set(array(1,2)));
	}
	
	public function testMaximum() {
		$t = new Set(1,2,2,3,4,5,5,1);
		$this->assertEqual($t->max(), 5);
		$this->assertEqual($t->max(0), null);
		$this->assertEqual($t->max(2), new Set(array(5, 4)));
	}
	
}

?>