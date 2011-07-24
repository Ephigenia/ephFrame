<?php

namespace ephFrame\test\util;

use ephFrame\util\Charset;

/**
 * @group Util
 */
class CharsetTest extends \PHPUnit_Framework_TestCase
{
	public function testIsUTF8()
	{
		$this->assertTrue(Charset::isUTF8('ö'));
	}
	
	public function testIsASCII()
	{
		$this->assertTrue(Charset::isASCII('abaslkdj'));
	}
	
	public function testIsNotASCII()
	{
		$this->assertFalse(Charset::isASCII('ö'));
	}
}
