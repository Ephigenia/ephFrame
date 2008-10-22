<?php

// init simpletest and framework
require_once dirname(__FILE__).'/../autorun.php';

/**
 * 	[SOME DOCU HERE WOULD BE NICE HEE!?]
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 18.08.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.test
 */
class TestHash extends UnitTestCase {
	
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.Hash');
	}
	
	public function testSort() {
		$test = new Hash();
		$test->append('Name', 'Hossa');
		$test->sort();
		$this->assertEqual($test->implode(), 'HossaName');
	}
	
	public function testToString() {
		$test = new Hash();
		$test->append('Name', 'Hossa');
		$this->assertEqual((string) $test, 'NameHossa');
	}
	
}

?>