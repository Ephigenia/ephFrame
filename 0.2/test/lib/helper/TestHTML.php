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
class TestHTML extends UnitTestCase {
	
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.helper.HTML');
	}
	
	public function testImg() {
		$HTML = new HTML();
		$this->assertEqual((string) $HTML->img('bild.jpg'), '<img src="bild.jpg" />');
		$this->assertEqual((string) $HTML->img('bild.jpg', array('class' => 'test class')), '<img class="test class" src="bild.jpg" />');
	}
	
	public function testLink() {
		$HTML = new HTML();
		$this->assertEqual((string) $HTML->link('../', 'link'), '<a href="../" title="link">link</a>');
		$this->assertEqual((string) $HTML->link('/', 'test'), '<a href="/" title="test">test</a>');
		$this->assertEqual((string) $HTML->link('', 'test'), '<a title="test">test</a>');
		$this->assertEqual((string) $HTML->link('', ''), '');
		$this->assertEqual((string) $HTML->link('?p=asdlkj&amp;=tralala', 'values & werte'), '<a href="?p=asdlkj&amp;=tralala" title="values &amp; werte">values & werte</a>');
	}
	
}

?>