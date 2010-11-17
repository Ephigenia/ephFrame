<?php

namespace ephFrame\test\core;

use ephFrame\core\Controller;
use ephFrame\HTTP\Request;
use ephFrame\HTTP\Response;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
	public function testResponseType()
	{
		$controller = new Controller(new Request());
	}
}