<?php

namespace ephFrame\test\HTTP;

use ephFrame\HTTP\Header;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
	public function testRendering()
	{
		$header = new Header(array(
			'Content-Type' => 'text/html; charset=UTF-8',
			'ETag' => 'content',
		));
		$this->assertEquals((string) $header, "Content-Type: text/html; charset=UTF-8\r\nETag: \"content\"");
	}
	
	public function testArrayAccess()
	{
		$header = new Header(array(
			'Content-Type' => 'text/html',
			'Pragma' => 'no-cache',
		));
		$header['Content-Type'] = 'text/plain';
		$header['Pragma'] = 'cache';
		$this->assertEquals((string) $header, "Content-Type: text/plain\r\nPragma: cache");
	}
	
	public function testSetters()
	{
		$header = new Header();
		$header->{'Content-Type'} = 'text/plain';
		$header->Pragma = 'cache';
		$this->assertEquals((string) $header, "Content-Type: text/plain\r\nPragma: cache");
	}
	
	public function testPropertyAccess()
	{
		$header = new Header(array(
			'Pragma' => 'no-cache',
		));
		$this->assertEquals($header->Pragma, 'no-cache');
		$this->assertEquals(isset($header['Pragma']), true);
		$this->assertEquals(!empty($header->Pragma), true);
		$this->assertEquals(!empty($header->Pragma), true);
	}
}