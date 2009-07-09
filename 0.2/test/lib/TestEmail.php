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
require_once dirname(__FILE__).'/../autorun.php';

/**
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestEmail extends UnitTestCase
{	
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.Email');
	}
	
	public function testConstructor() {
		$m = new Email('thomas@nomoresleep.net');
		$m->addTo("thomas@michelbach.biz");
		$m->addTo("thomas@michelbach.biz");
		$m->addTo("torsten@nomoresleep.net");
		print_r($m);
	}
	
}