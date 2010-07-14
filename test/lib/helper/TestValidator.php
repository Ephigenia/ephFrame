<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
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
 * Test for {@link Validator} Helper class
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2009-09-11
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestValidator extends UnitTestCase
{
	public function setUp() 
	{
		ephFrame::loadClass('ephFrame.lib.helper.Validator');
	}
	
	public function testEmail() 
	{
		$emails = array(
			'l.fgetgwxpv@manexam.net',
		);
		foreach($emails as $email) {
			$this->assertEqual(Validator::email($email), true);
		}
	}	
}