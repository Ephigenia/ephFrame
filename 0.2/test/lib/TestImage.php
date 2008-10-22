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
 * 	[SOME DOCU HERE WOULD BE NICE HEE!?]
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 18.08.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.test
 */
class TestImage extends UnitTestCase {
	
	/**
	 * 	@var Image
	 */
	public $testImage;
	
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.Image');
		$this->testImage = new Image(dirname(__FILE__).'/../tmp/Blue_Box_in_museum.jpg');
	}
	
	public function testType() {
		$this->assertEqual($this->testImage->type(), Image::TYPE_JPG);
	}
	
	public function testIsType() {
		$this->assertTrue($this->testImage->isType(Image::TYPE_JPG), true);
	}
	
	public function testExtension() {
		$this->assertEqual($this->testImage->extension(), 'jpg');
	}
	
	public function testChannels() {
		$this->assertEqual($this->testImage->channels(), 3);
	}
	
	public function testHasChannels() {
		$this->assertEqual($this->testImage->hasChannels(3), true);
	}
	
	public function testHandle() {
		$this->assertTrue(is_resource($this->testImage->handle()), true);
	}
	
	public function testAspectRatio() {
		$this->assertEqual($this->testImage->aspectRatio(2), 1.00);
	}
	
	public function testWidth() {
		$this->assertEqual($this->testImage->width(), 461);
	}
	
	public function testHeight() {
		$this->assertEqual($this->testImage->height(), 461);
	}
	
	public function testIsPanelFormat() {
		$this->assertEqual($this->testImage->isPanelFormat(), false);
	}
	
}

?>