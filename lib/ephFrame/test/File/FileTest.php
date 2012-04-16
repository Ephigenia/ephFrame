<?php

namespace ephFrame\test\view;

use ephFrame\File\File;

/**
 * @group File
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new File(__FILE__);
	}
	
	public function testBasename()
	{
		$this->assertEquals('FileTest.php', $this->fixture->basename());
		$this->assertEquals('FileTest', $this->fixture->basename('.php'));
		$this->assertEquals('FileTest', $this->fixture->basename(false));
	}
	
	public function testFilename()
	{
		$this->assertEquals('FileTest.php', $this->fixture->filename());
		$this->assertEquals('FileTest', $this->fixture->filename('.php'));
		$this->assertEquals('FileTest', $this->fixture->filename(false));
	}
	
	public function testExists()
	{
		$this->assertTrue($this->fixture->exists());
	}
	
	public function testReadable()
	{
		$this->assertTrue($this->fixture->readable());
	}
	
	public function testMimeType()
	{
		$this->assertEquals('text/x-php', $this->fixture->mimeType());
	}
	
	public function testSize()
	{
		$this->assertGreaterThan(100, $this->fixture->size());
	}
}