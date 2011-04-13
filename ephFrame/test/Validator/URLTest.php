<?php

namespace ephFrame\test\Validator;

use ephFrame\Validator\URL;

class URLTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new URL();
	}
	
	public function testInvalidValues()
	{
		return array(
			array('abcd.de'),
			array('www.marceleichner.de'),
			array('://www.marceleichner.de'),
			array('/www.marceleichner.de/'),
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
			array('http://www.marceleichner.de'),
			array('http://www.marceleichner.de/directory'),
			array('http://www.marceleichner.de/directory/'),
			array('http://user.marceleichner.de'),
			array('http://www.marceleichner.de:89999/directory/file.php?query=param&amp;array[0]=value,with,comma.csv'),
			array('http://subdomain.marceleichner.de:89999/directory/file.php?query=param&amp;array[0]=value,with,comma.csv'),
		);
	}
	
	/**
	 * @dataProvider testValidValues()
	 */
	public function testValid($value)
	{
		$this->assertTrue($this->fixture->validate($value));
	}
	
	public function testCustomProtocols()
	{
		$this->fixture->protocols[] = 'ftp';
		$this->assertTrue($this->fixture->validate('http://www.marceleichner.de'));
		$this->assertTrue($this->fixture->validate('ftp://www.marceleichner.de'));
	}
	
	public function testAllProtocols()
	{
		$this->fixture->protocols = false;
		$this->assertTrue($this->fixture->validate('mailto://www.marceleichner.de'));
		$this->assertTrue($this->fixture->validate('something://www.marceleichner.de'));
	}
}