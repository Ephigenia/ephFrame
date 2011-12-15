<?php

namespace ephFrame\test\Filter;

use ephFrame\Filter\Basename;

class BasenameTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Basename();
	}
	
	public function testSimpleValues()
	{
		return array(
			array('style.css', 'style.css'),
			array('style', 'style'),
			array('style.css.sass', 'style.css.sass'),
		);
	}
	
	/**
	 * @dataProvider testSimpleValues()
	 */
	public function testSimple($left, $right)
	{
		$this->assertEquals($right, $this->fixture->apply($left));
	}
	
	public function testLongFilename()
	{
		$longFilename = str_repeat('a', 300);
		$result = str_repeat('a', 255);
		$this->assertEquals($result, $this->fixture->apply($longFilename));
	}
	
	public function testLongFilenameWithExtension()
	{
		$longFilename = str_repeat('a', 300).'.css.html';
		$result = str_repeat('a', 255 - 9).'.css.html';
		$this->assertEquals($result, $this->fixture->apply($longFilename));
	}
	
	public function testInvalidCharactersValues()
	{
		return array(
			array('filename;.css','filename.css'),
			array('../Präsentation?.doc.xls', 'Präsentation.doc.xls'),
		);
	}
	
	/**
	 * @dataProvider testInvalidCharactersValues()
	 */
	public function testInvalidCharacters($left, $right)
	{
		$this->paranoid = false;
		$this->assertEquals($right, $this->fixture->apply($left));
	}
	
	public function testParanoidValues()
	{
		return array(
			array('filename;.css','filename.css'),
			array('../Präsentation?.doc.xls', 'Prsentation.doc.xls'),
		);
	}
	
	/**
	 * @dataProvider testParanoidValues()
	 */
	public function testParanoid($left, $right)
	{
		$this->fixture->paranoid = true;
		$this->assertEquals($right, $this->fixture->apply($left));
	}
}