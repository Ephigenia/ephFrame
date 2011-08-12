<?php

namespace ephFrame\test\HTML\Form;

use ephFrame\HTML\Form\Element\URL;

/**
 * @group Element
 */
class URLTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new URL('url');
	}
	
	public function testValidatorError()
	{
		$this->fixture->submit('http://');
		$this->assertTrue((bool) $this->fixture->error());
	}
	
	public function testValidationSuccess()
	{
		$this->fixture->submit('http://www.marceleichner.de');
		$this->assertFalse($this->fixture->error());
	}
	
	public function testDefaultProtocolUsage()
	{
		$this->fixture->submit('www.marceleichner.de');
		$this->assertFalse($this->fixture->error());
	}
	
	public function testDefaultProtocol()
	{
		$this->fixture->defaultProtocol = false;
		$this->fixture->submit('www.marceleichner.de');
		$this->assertTrue((bool) $this->fixture->error());
	}
	
	public function testSubmit()
	{
		$field = new URL('url', null, array());
		$field->submit('www.ephigenia.de');
		$this->assertEquals($field->data, 'http://www.ephigenia.de');
	}
	
	public function testAddDefault()
	{
		$field = new URL('url', null, array(
			'defaultProtocol' => 'http',
		));
		$field->submit('ftp://www.ephigenia.de');
		$this->assertEquals($field->data, 'ftp://www.ephigenia.de');
	}
	
	public function testAddNoDefaultEmpty()
	{
		$field = new URL('url', null, array('defaultProtocol' => null));
		$field->submit('www.ephigenia.de');
		$this->assertEquals($field->data, 'www.ephigenia.de');
	}
	
	public function testAddNoDefaultFalse()
	{
		$field = new URL('url', null, array('defaultProtocol' => false));
		$field->submit('www.ephigenia.de');
		$this->assertEquals($field->data, 'www.ephigenia.de');
	}
}