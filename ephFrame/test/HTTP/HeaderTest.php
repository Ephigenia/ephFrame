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
	
	public function testSetter()
	{
		$header = new Header(array(
			'Content-Type' => 'text/html',
			'Pragma' => 'no-cache',
		));
		$header->{'Content-Type'} = 'text/plain';
		$header->Pragma = 'cache';
		$this->assertEquals((string) $header, "Content-Type: text/plain\r\nPragma: cache");
	}
}