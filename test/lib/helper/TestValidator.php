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
		Library::load('ephFrame.lib.util.Validator');
	}
	
	public function testEmail() 
	{
		$emails = array(
			// simple examles
			'marcel.eichner@ephigenia.de',
			'marcel.eichner@ephigenia.co.uk',
			'm.e@ephigenia.de',
			'm.e.f@ephgienia.de',
			'l.fgetgwxpv@manexam.net',
			'~user1@system.com.edu.gov',
			// unicode examples from wikipedia (http://en.wikipedia.org/wiki/Email_address)
			// 'Pelé@example.com',
			// 'δοκιμή@παράδειγμα.δοκιμή',
			// '甲斐@黒川.日本',
		);
		foreach($emails as $email) {
			$this->assertEqual(Validator::email($email), true);
		}
	}
}