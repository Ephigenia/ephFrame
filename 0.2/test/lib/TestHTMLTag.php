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
class TestHTMLTag extends UnitTestCase {
	
	public function setUp() {
		loadClass('ephFrame.lib.HTMLTag');
	}
	
	public function testRender() {
		$tag = new HTMLTag('a', array('href' => 'http://www.ephigenia.de'), 'label');
		$this->assertEqual((string) $tag, '<a href="http://www.ephigenia.de">label</a>');
	}
	
	public function testQuoting() {
		$tag = new HTMLTag('img', array('src' => 'test.jpg', 'alt' => '"Hallo" - he said'));
		$this->assertEqual((string) $tag, '<img src="test.jpg" alt="&quot;Hallo&quot; - he said" />');
	}
	
}

?>