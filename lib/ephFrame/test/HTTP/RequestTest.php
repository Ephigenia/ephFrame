<?php

namespace ephFrame\test\HTTP;

use ephFrame\HTTP\Request;
use ephFrame\HTTP\Header;
use ephFrame\HTTP\RequestMethod;

/**
 * @group HTTP
 */
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
		$this->assertEquals($request->query['test2'], $_GET['test2']);
	}
	
	public function testAutoFill()
	{
		$_SERVER['REQUEST_URI'] = '/user/1/show';
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$request = new Request();
		$this->assertEquals(RequestMethod::POST, $request->method);
		$this->assertEquals('/user/1/show', $request->path);
		unset($_SERVER['REQUEST_URI']);
		unset($_SERVER['REQUEST_METHOD']);
	}
	
	public function testFiles()
	{
		$request = new Request(null, null, null, array(), array('files'));
		$this->assertEquals($request->files, array('files'));
	}
	
	public function testRequestMethod()
	{
		$this->assertEquals($this->request->method, RequestMethod::POST);
	}
	
	public function test__construct()
	{
		$this->assertEquals((string) $this->request, 
			"POST /path with space/ HTTP/1.1\r\n".
			"\r\n".
			"param1=value1&param2=spaced+value"
		);
	}
	
	public function testIsMethod()
	{
		$this->assertTrue($this->request->isMethod(RequestMethod::POST));
		$this->assertTrue($this->request->isMethod(array(RequestMethod::POST)));
		$this->assertFalse($this->request->isMethod(array(RequestMethod::GET)));
	}
	
	public function testServerVars()
	{
		$_SERVER['HTTP_USER_AGENT'] = 'User Agent Test String';
		$request = new Request(RequestMethod::GET);
		$this->assertEquals($request->header['user-agent'], 'User Agent Test String');
		unset($_SERVER['HTTP_USER_AGENT']);
	}
	
	public function testQueryExtraction()
	{
		$request = new Request(RequestMethod::GET, '/something?id=123');
		$this->assertEquals($request->path, '/something');
	}
	
	public function testIsSecure()
	{
		$this->assertFalse($this->request->isSecure());
		$this->request->header['https'] = 'on';
		$this->assertTrue($this->request->isSecure());
	}
	
	public function testIsAjax()
	{
		$this->assertFalse($this->request->isAjax());
		$this->request->header['x-requested-with'] = 'XMLHttpRequest';
		$this->assertTrue($this->request->isAjax());
	}
	
	public function testGetRendering()
	{
		$this->request->method = RequestMethod::GET;
		$this->assertEquals((string) $this->request, 
			"GET /path with space/?param1=value1&param2=spaced+value HTTP/1.1"
		);
	}
}
