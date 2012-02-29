<?php

namespace ephFrame\test\HTTP;

use ephFrame\HTTP\StatusCode;

/**
 * @group HTTP
 */
class StatusCodeTest extends \PHPUnit_Framework_TestCase
{
	public function messageEqualValues()
	{
		return array(
			array(200, 'OK'),
			array(StatusCode::OK, 'OK'),
			array('200', 'OK'),
			array(404, 'Not Found'),
			array(500, 'Internal Server Error'),
		);
	}
	
	/**
	 * @dataProvider messageEqualValues
	 */
	public function testMessage($status, $expectedResult)
	{
		$this->assertEquals(StatusCode::message($status), $expectedResult);
	}
	
	public function testNoMessage()
	{
		$this->assertFalse(StatusCode::message('asdlkj'));
	}
	
	public function isErrorTrueValues()
	{
		return array(
			array(400), array(401), array(501), array('400'), array('  401')
		);
	}
	
	/**
	 * @dataProvider isErrorTrueValues
	 */
	public function testIsError($statusCode)
	{
		$this->assertTrue(StatusCode::isError($statusCode));
	}
}