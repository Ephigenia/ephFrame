<?php

namespace ephFrame\test\core;

use ephFrame\core\Controller;
use ephFrame\HTTP\Request;
use ephFrame\HTTP\Response;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->controller = new Controller(new Request('GET', '/'));
	}
	
	public function testAction()
	{
		$this->controller->action('index');
	}
	
	public function testResponseType()
	{
		
	}
}