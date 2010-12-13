<?php

namespace ephFrame\test\HTTP;

use ephFrame\HTTP\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$response = new Response(404, null, 'NOT FOUND');
		$this->assertEquals((string) $response, "HTTP 1.1 404 Not Found\r\n\r\nNOT FOUND");
	}
	
	public function testStatusChange()
	{
		$response = new Response(200);
		$response->status = 404;
		$this->assertEquals((string) $response, 'HTTP 1.1 404 Not Found');
	}
	
	public function testProtocoll()
	{
		$response = new Response(200);
		$response->protocol = 'HTTP 1.0';
		$this->assertEquals((string) $response, 'HTTP 1.0 200 OK');
	}
	
	public function testBodyAppend()
	{
		$response = new Response(200);
		$response->body .= 'APPENDED';
		$this->assertEquals((string) $response, "HTTP 1.1 200 OK\r\n\r\nAPPENDED");
	}
}
