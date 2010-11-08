<?php

namespace ephFrame\test\lib\HTTP;

use ephFrame\lib\HTTP\Header;

class HeaderTest extends PHPUnit\Framework\PHPUnit_Framework_TestCase
{
	public function testRendering()
	{
		$Header = new ephFrame\lib\HTTP\Header(array(
			'Content-Type' => 'text/html; charset=UTF-8',
		));
		$this->assertEquals((string) $Header, 'Content-Type: text/html; charset=UTF-8');
	}
}