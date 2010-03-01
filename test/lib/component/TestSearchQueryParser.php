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
 * @filesource
 */

// init simpletest and framework
require_once dirname(__FILE__).'/../../autorun.php';

/**
 * [SOME DOCU HERE WOULD BE NICE HEE!?]
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestSeachQueryParser extends UnitTestCase
{	
	public function setUp() {
		loadClass('ephFrame.lib.component.SearchQueryParser');
	}
	
	public function test() {
		$testArray = array(
			'%25C3%25A4' => array('%C3%A4'),
			'%22marcel+eichner+%26+illustration%22' => array('marcel eichner & illustration'),
			'%22marcel+eichner%22' => array('marcel eichner')
		);
		foreach($testArray as $input => $output) {
			$parser = new SearchQueryParser($input);
		}
	}
	
}