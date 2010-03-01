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
 * @version		$Revision$
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
class TestCollection extends UnitTestCase {
	
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.Collection');
	}
	
	public function testCollectionUnique() {
		$c = new Collection();
		$c->add('A');
		$c->add('B');
		$c->add('C');
		$this->assertEqual((string) $c->implode(','), 'A,B,C');
		// test adding of an element that is allready there
		$c->add('A');
		$this->assertEqual((string) $c->implode(','), 'A,B,C');
	}
	
}
