<?php

namespace ephFrame\test\HTTP;

use ephFrame\HTTP\StatusCode;

class StatusCodeTest extends \PHPUnit_Framework_TestCase
{
	public function testMessage()
	{
		foreach (array(
			200 => 'OK',
			StatusCode::OK => 'OK',
			404 => 'Not Found',
			500 => 'Internal Server Error',
			) as $l => $r) {
			$this->assertEquals(StatusCode::message($l), $r);
		}
	}
	
	public function isError()
	{
		foreach (array(
			404 => true,
			200 => false,
			) as $l => $r) {
			$this->assertEquals(StatusCode::isError($l), $r);
		}
	}
}