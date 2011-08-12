<?php

namespace ephFrame\test\core;

use 
	\ephFrame\core\Controller,
	\ephFrame\HTTP\Request,
	\ephFrame\HTTP\Response
	;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Controller(new Request('GET', '/'));
	}
	
	public function testAction()
	{
		$this->assertTrue($this->fixture->action('index'));
	}
}