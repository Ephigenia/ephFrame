<?php

namespace ephFrame\test\util;

use ephFrame\util\Inflector;

/**
 * @group Util
 */
class InflectorTest extends \PHPUnit_Framework_TestCase
{
	public function pluralizeEqualValues()
	{
		return array(
			array('news', 'news'),
			array('NewS', 'NewS'),
			array('User', 'Users'),
			array('user', 'users'),
			array('Country', 'Countries'),
			array('boy', 'boys'),
			array('girl', 'girls'),
			array('hero', 'heroes'),
			array('potato', 'potatoes'),
			array('volcano', 'volcanoes'),
			array('dish', 'dishes'),
			array('witch', 'witches'),
			array('phase', 'phases'),
			array('cherry', 'cherries'),
			array('lady', 'ladies'),
			array('bus', 'busses'),
			array('life', 'lives'),
			array('kiss', 'kisses'),
		);
	}
	
	/**
	 * @dataProvider pluralizeEqualValues
	 */
	public function testPluralize($k, $v) 
	{ 
		$this->assertEquals(Inflector::pluralize($k), $v);
	}
	
	public function singularizeEqualValues()
	{
		return array(
			array('users', 'user'),
			array('Countries', 'Country'),
			array('kisses', 'kiss'),
			array('judges', 'judge'),
			array('noplural', 'noplural'),
		);
	}
	
	/**
	 * @dataProvider singularizeEqualValues
	 */
	public function testSingularize($left, $right) 
	{
		$this->assertEquals(Inflector::singularize($left), $right);
	}
	
	public function camelizeEqualValues() 
	{
		return array(
			array('my_class_name', 'myClassName'),
			array('my class näme', 'myClassNäme'),
			array("my\n class näme", 'myClassNäme'),
			array('app_my_class', 'appMyClass'),
			array('_first second', 'firstSecond'),
			array('__first second__', 'firstSecond'),
		);
	}
	
	/**
	 * @dataProvider camelizeEqualValues
	 */
	public function testCamelize($left, $right) 
	{
		$this->assertEquals(Inflector::camelize($left), $right);
	}
	
	public function testCamilizeUpper()
	{
		$this->assertEquals(Inflector::camelize('app_my_class', true), 'AppMyClass');
	}
	
	public function underscoreEqualValues()
	{
		return array(
			array('tree simple words', 'tree_simple_words'),
			array('youAreSo   Great', 'you_are_so_great'),
			array('testTHe_great', 'test_t_he_great'),
			array('  master Testa', 'master_testa'),
			array(' @freak 123   ', '@freak_123'),
			array('__underscore', '_underscore'),
			array(' __underscore space', '_underscore_space'),
		);
	}
	
	/**
	 * @dataProvider underscoreEqualValues
	 */
	public function testUnderscore($left, $right)
	{
		$this->assertEquals(Inflector::underscore($left), $right);
	}
	
	public function underscoreAlternateDelimeterEqualValues()
	{
		return array(
			array('tree simple words', 'tree-simple-words'),
			array('youAreSo    Great', 'you-are-so-great'),
			array('testTHe_great', 'test-t-he_great'),
			array('  master Testa', 'master-testa'),
			array(' @freak 123   ', '@freak-123'),
		);
	}
	
	/**
	 * @dataProvider underscoreAlternateDelimeterEqualValues
	 */
	public function testUnderscoreAlternateDelimeter($left, $right)
	{
		$this->assertEquals(Inflector::underscore($left, '-'), $right);
	}
}