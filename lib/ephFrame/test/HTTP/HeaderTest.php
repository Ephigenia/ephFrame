<?php

namespace ephFrame\test\HTTP;

use ephFrame\HTTP\Header;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->header = new Header(array(
			'Content-Type' => 'text/html; charset=UTF-8',
			'Content-Length' => 1000,
			'Pragma' => 'none',
			'ETag' => 'e64486',
			'x-custom' => 'some free text field',
		));
	}
	
	public function testRendering()
	{
		$this->assertEquals((string) $this->header,
			"Content-Type: text/html; charset=UTF-8\r\n".
			"Content-Length: 1000\r\n".
			"Pragma: none\r\n".
			"ETag: \"e64486\"\r\n".
			"x-custom: some free text field"
		);
	}
	
	public function testArrayAccessRead()
	{
		$this->assertEquals($this->header['Content-Type'], 'text/html; charset=UTF-8');
	}
	
	/**
	 * @depends testArrayAccessRead
	 */
	public function testArrayAccessWrite()
	{
		$this->header['Content-Length'] = 2000;
		$this->assertEquals($this->header['Content-Length'], 2000);
	}
	
	public function testPropertyAccessRead()
	{
		$this->assertEquals($this->header->{'Content-Type'}, 'text/html; charset=UTF-8');
	}
	
	/**
	 * @depends testPropertyAccessRead
	 */
	public function testPropertyAccessWrite() 
	{
		$this->header->{'Content-Length'} = 3000;
		$this->assertEquals($this->header->{'Content-Length'}, 3000);
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testSend() 
	{
		$this->header->send();
	}
}