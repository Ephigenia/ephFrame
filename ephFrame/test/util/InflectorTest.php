<?php

namespace ephFrame\test\util;

use ephFrame\util\Inflector;

class InflectorTest extends \PHPUnit_Framework_TestCase
{
	public function testPluralize() 
	{
		$tests = array(
			'User' => 'Users',
			'user' => 'users',
			'Country' => 'Countries',
			'boy' => 'boys',
			'girl' => 'girls',
			'hero' => 'heroes',
			'potato' => 'potatoes',
			'volcano' => 'volcanoes',
			'dish' => 'dishes',
			'witch' => 'witches',
			'phase' => 'phases',
			'cherry' => 'cherries',
			'lady'	=> 'ladies',
			'bus' => 'busses',
			'life' => 'lives',
			'kiss' => 'kisses',
		);
		foreach($tests as $k => $v) {
			$this->assertEquals(Inflector::pluralize($k), $v);
		}
	}
	
	public function testSingularize() 
	{
		$tests = array(
			'users' => 'user',
			'Countries' => 'Country',
			'kisses' => 'kiss',
			'judges' => 'judge',
		);
		foreach($tests as $left => $right) {
			$this->assertEquals(Inflector::singularize($left), $right);
		}
	}
	
	public function testCamelize() 
	{
		$tests = array(
			'my_class_name' => 'myClassName',
			'my class näme' => 'myClassNäme',
			"my\n class näme" => 'myClassNäme',
		);
		foreach($tests as $left => $right) {
			$this->assertEquals(Inflector::camelize($left), $right);
		}
		$this->assertEquals(Inflector::camelize('my class näme', true), 'MyClassNäme');
	}
	
	public function testUnderscore() 
	{
		$a = array(
			'tree simple words' => 'tree_simple_words',
			'youAreSo   Great' => 'you_are_so_great',
			'testTHe_great' => 'test_t_he_great',
			'  master Testa' => 'master_testa',
			' @freak 123   ' => '@freak_123',
			'__underscore' => '_underscore',
			' __underscore space' => '_underscore_space',
		);
		foreach($a as $input => $output) {
			$this->assertEquals(Inflector::underscore($input), $output);
		}
	}
	
	public function testUnderscoreAlternateDelimeter()
	{
		foreach(array(
			'tree simple words' => 'tree-simple-words',
			'youAreSo    Great' => 'you-are-so-great',
			'testTHe_great' => 'test-t-he_great',
			'  master Testa' => 'master-testa',
			' @freak 123   ' => '@freak-123',
			) as $k => $v) {
			$this->assertEquals(Inflector::underscore($k, '-'), $v);
		}
	}
}