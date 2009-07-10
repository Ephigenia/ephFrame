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
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestObjectSet extends UnitTestCase {
	
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.ObjectSet');
	}
	
	public function testGeneral() {
		$set = new ObjectSet('IndexedArray');
		$set->add(new IndexedArray('test'));
		$set->add(new IndexedArray('zero'));
		$set->add(new IndexedArray(1));
		$set->sort();
		$this->assertEqual((string) $set, 'testzero1');
	}
	
}