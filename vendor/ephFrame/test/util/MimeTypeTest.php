<?php

namespace ephFrame\test\util;

use ephFrame\util\MimeType;

/**
 * @group Util
 */
class MimeTypeTest extends \PHPUnit_Framework_TestCase
{
	public function testGetByFilenameValues()
	{
		return array(
			array('myfilename.csv','text/comma-separated-values'),
			array('multiple.ext.txt', 'text/plain'),
			array('txt', 'text/plain'),
			array('.txt', 'text/plain'),
			array('.', false),
			array('', false),
			array('nothing', false),
		);
	}
	
	/**
	 * @dataProvider testGetByFilenameValues
	 */
	public function testGetByFilename($filename, $result)
	{
		$this->assertEquals(MimeType::get($filename), $result);
	}
	
	public function testGetByExtensionValues()
	{
		return array(
			array('txt', 'text/plain'),
			array('text', false),
		);
	}
	
	/**
	 * @dataProvider testGetByExtensionValues
	 */
	public function testGetByExtension($extension, $result)
	{
		$this->assertEquals(MimeType::get($extension), $result);
	}
}