<?php

namespace ephFrame\test\Validator;

use ephFrame\Validator\Email;

class EmailTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new EMail();
	}
	
	public function testInvalidValues()
	{
		return array(
			array('nothing'),
			array('@yahoo.de'),
			array('me@.de'),
			array('me.de'),
			array('-@-'),
			array('me@.de.de'),
			array('stupid@email@addy.com'),
		);
	}
	
	/**
	 * @dataProvider testInvalidValues()
	 */
	public function testInvalid($value)
	{
		$this->assertFalse($this->fixture->validate($value));
	}
	
	public function testValidValues()
	{
		return array(
			array('marcel.eichner@ephigenia.de'),
			array('marcel.eichner@ephigenia.co.uk'),
			array('m.e@ephigenia.de'),
			array('m.e.f@ephgienia.de'),
			array('l.fgetgwxpv@manexam.net'),
			array('~user1+label@system.com.edu.gov'),
		);
	}
	
	/**
	 * @dataProvider testValidValues()
	 */
	public function testValid($value)
	{
		$this->assertTrue($this->fixture->validate($value));
	}
	
	public function testUnicodeValues()
	{
		// unicode examples from wikipedia (http://en.wikipedia.org/wiki/Email_address)
		return array(
			array('Pelé@example.com'),
			array('δοκιμή@παράδειγμα.δοκιμή.gr'),
			array('甲斐@黒川.日本.ja'),
		);
	}
	
	/**
	 * @dataProvider testUnicodeValues()
	 */
	public function testUnicode($value)
	{
		$this->fixture->unicode = true;
		$this->assertTrue($this->fixture->validate($value));
	}
}