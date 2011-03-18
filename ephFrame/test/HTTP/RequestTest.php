<?php

namespace ephFrame\test\HTTP;

use ephFrame\HTTP\Request;
use ephFrame\HTTP\Header;
use ephFrame\HTTP\RequestMethod;

class RequestTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->request = new Request(RequestMethod::POST, '/path with space/', new Header(), array(
			'param1' => 'value1',
			'param2' => 'spaced value',
		));
	}
	
	public function testFillFromPost()
	{
		$_POST['test'] = 'testvalue';
		$request = new Request(RequestMethod::POST, '/path/');
		$this->assertEquals($request->data['test'], $_POST['test']);
	}
	
	public function testFillFromGet()
	{
		$_GET['test2'] = 'testvalue';
		$request = new Request(RequestMethod::GET, '/path/');
		$this->assertEquals($request->data['test2'], $_GET['test2']);
	}
	
	public function testRequestMethod()
	{
		$this->assertEquals($this->request->method, RequestMethod::POST);
	}
	
	public function test__construct()
	{
		$this->assertEquals((string) $this->request, 
			"POST /path with space/ HTTP 1.1\r\n".
			"\r\n".
			"param1=value1&param2=spaced+value"
		);
	}
	
	public function testIsSecure()
	{
		$this->assertFalse($this->request->isSecure());
	}
	
	public function testIsAjax()
	{
		$this->assertFalse($this->request->isSecure());
	}
	
	public function testGetRendering()
	{
		$this->request->method = RequestMethod::GET;
		$this->assertEquals((string) $this->request, 
			"GET /path with space/?param1=value1&param2=spaced+value HTTP 1.1"
		);
	}
}
