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
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
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
class TestSGMLBeautifier extends UnitTestCase {
	
	public function setUp() {
		ephFrame::loadClass('ephFrame.lib.SGMLBeautifierFilter');
	}
	
	public function testBeautifier() {
		$testString = '<html><head></head><body><h1><strong>Bold</strong></h1></body></html>';
		$beautifier = new SGMLBeautifierFilter();
		$this->assertEqual($beautifier->apply($testString), '<html>
	<head>
	</head>
	<body>
		<h1>
			<strong>Bold</strong>
		</h1>
	</body>
</html>');
	}
	
}