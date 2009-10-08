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
 */

// init simpletest and framework
require_once dirname(__FILE__).'/../../autorun.php';

/**
 * Unit Tests for the {@link HTML} Helper class from the {@link ephFrame}
 * framework
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestHTML extends UnitTestCase
{
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.helper.HTML');
	}
	
	public function testEmail() {
		$HTML = new HTML();
		$this->assertEqual((string) $HTML->email('love@ephigenia.de'), '<a href="mailto:&#108;&#111;&#118;&#101;&#64;&#101;&#112;&#104;&#105;&#103;&#101;&#110;&#105;&#97;&#46;&#100;&#101;" title="&#108;&#111;&#118;&#101;&#64;&#101;&#112;&#104;&#105;&#103;&#101;&#110;&#105;&#97;&#46;&#100;&#101;">&#108;&#111;&#118;&#101;&#64;&#101;&#112;&#104;&#105;&#103;&#101;&#110;&#105;&#97;&#46;&#100;&#101;</a>');
		$this->assertEqual((string) $HTML->email('love@ephigenia.de', 'Other Label'), '<a href="mailto:&#108;&#111;&#118;&#101;&#64;&#101;&#112;&#104;&#105;&#103;&#101;&#110;&#105;&#97;&#46;&#100;&#101;" title="Other Label">Other Label</a>');
	}
	
	public function testImg() {
		$HTML = new HTML();
		$this->assertEqual((string) $HTML->img('bild.jpg'), '<img src="'.WEBROOT.'static/img/bild.jpg" alt="" />');
		$this->assertEqual((string) $HTML->img('bild.jpg', array('class' => 'test class')), '<img class="test class" src="'.WEBROOT.'static/img/bild.jpg" alt="" />');
	}
	
	public function testLink() {
		$HTML = new HTML();
		$this->assertEqual((string) $HTML->link('../', 'link'), '<a href="../" title="link">link</a>');
		$this->assertEqual((string) $HTML->link('/', 'test'), '<a href="/" title="test">test</a>');
		$this->assertEqual((string) $HTML->link('', 'test'), '<a title="test">test</a>');
		$this->assertEqual((string) $HTML->link('', ''), '');
		$this->assertEqual((string) $HTML->link('?p=asdlkj&amp;=tralala', 'values & werte'), '<a href="?p=asdlkj&amp;=tralala" title="values &amp; werte">values & werte</a>');
	}
	
} // END TestHTML class