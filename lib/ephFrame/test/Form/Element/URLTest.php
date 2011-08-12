<?php

namespace ephFrame\test\Form\Element;

use ephFrame\HTML\Form\Element\URL;

/**
 * @group Element
 */
class URLTest extends \PHPUnit_Framework_TestCase 
{
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