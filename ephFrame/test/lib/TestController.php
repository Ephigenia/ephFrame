<?php

class ControllerTest extends PHPUnit_Framework_TestCase
{
	public function testConstruct()
	{
		$controller = new Controller(new HTTPRequest(), array());
	}
}