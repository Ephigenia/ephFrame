<?php

namespace ephFrame\test\core;

use ephFrame\core\Controller;
use ephFrame\HTTP\Request;
use ephFrame\HTTP\Response;

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
	
	/**
	 * @expectedException \ephFrame\view\TemplateNotFoundException
	 */
	public function test__toString()
	{
		$this->fixture->__toString();
	}
}