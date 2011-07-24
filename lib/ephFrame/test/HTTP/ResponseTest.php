<?php

namespace ephFrame\test\HTTP;

use ephFrame\HTTP\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @covers \ephFrame\HTTP\Response::__construct
	 */	
	public function setUp()
	{
		$this->response = new Response(404, null, 'Not Found');
	}
	
	public function test__toString()
	{
		$this->assertEquals((string) $this->response, "HTTP/1.1 404 Not Found\r\n\r\nNot Found");
	}
	
	public function testBodyChange()
	{
		$this->response->body = 'Document was not found on the server';
		$this->assertEquals((string) $this->response, "HTTP/1.1 404 Not Found\r\n\r\nDocument was not found on the server");
	}
	
	public function testBodyWithSpaces()
	{
		$this->response->body = 'Document was not found on the server      ';
		$this->assertEquals((string) $this->response, "HTTP/1.1 404 Not Found\r\n\r\nDocument was not found on the server");
	}
	
	/**
	 * @covers ephFrame\HTTP\Response::__toString
	 * @depends testBodyChange
	 */
	public function testHeaderAdd()
	{
		$this->response->header['Content-Length'] = strlen($this->response->body);
		$this->assertEquals((string) $this->response,
			"HTTP/1.1 404 Not Found\r\n".
			"Content-Length: 9\r\n".
			"\r\n".
			"Not Found"
		);
	}
}
