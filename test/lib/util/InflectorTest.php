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
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

// init simpletest and framework
require_once dirname(__FILE__).'/../autorun.php';

/**
 * [SOME DOCU HERE WOULD BE NICE HEE!?]
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 06.10.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestInflector extends UnitTestCase 
{
	public function setUp() 
	{
		Library::load('ephFrame.lib.util.Inflector');
	}
	
	public function testPluralize() 
	{
		$tests = array(
			'User'  => 'Users',
			'user'	=> 'users',
			'Country'	=> 'Countries',
			'boy' => 'boys',
			'girl' => 'girls',
			// -ies rule
			'cherry' => 'cherries',
			'lady'	=> 'ladies',
			// -oes rule,
			'hero' => 'heroes', 'potato' => 'potatoes', 'volcano' => 'volcanoes'
		);
		foreach($tests as $left => $right) {
			$this->assertEqual(Inflector::pluralize($left), $right);
		}
	}
	
	public function testCamelize() 
	{
		$tests = array(
			'my_class_name' => 'myClassName',
			'my class näme' => 'myClassNäme',
			'my'.LF.' class näme' => 'myClassNäme',
		);
		foreach($tests as $left => $right) {
			$this->assertEqual(Inflector::camelize($left), $right);
		}
		$this->assertEqual(Inflector::camelize('my class näme', true), 'MyClassNäme');
	}
	
	public function testUnderscore() 
	{
		$a = array(
			'hallo my name is Earl' => 'hallo_my_name_is_earl',
			'youAreSo Great' => 'you_are_so_great',
			'testTHe_great' => 'test_t_he_great',
			'  master Testa' => 'master_testa',
			' @freak 123   ' => '@freak_123'
		);
		foreach($a as $input => $output) {
			$this->assertEqual(Inflector::underscore($input), $output);
		}
		$this->assertEqual(Inflector::underscore(' space is good'.LF, '-'), 'space-is-good');
	}	
}