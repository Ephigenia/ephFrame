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
		$this->assertEquals('http://www.ephigenia.de', $field->data);
	}
	
	public function testAddDefault()
	{
		$field = new URL('url', null, array(
			'defaultProtocol' => 'http',
		));
		$field->submit('ftp://www.ephigenia.de');
		$this->assertEquals('ftp://www.ephigenia.de', $field->data);
	}
	
	public function testAddNoDefaultEmpty()
	{
		$field = new URL('url', null, array('defaultProtocol' => null));
		$field->submit('www.ephigenia.de');
		$this->assertEquals('www.ephigenia.de', $field->data);
	}
	
	public function testAddNoDefaultFalse()
	{
		$field = new URL('url', null, array('defaultProtocol' => false));
		$field->submit('www.ephigenia.de');
		$this->assertEquals('www.ephigenia.de', $field->data);
	}
}