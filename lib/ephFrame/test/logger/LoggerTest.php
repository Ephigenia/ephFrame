<?php

namespace ephFrame\test\logger;

use
	\ephFrame\logger\Logger,
	\ephFrame\logger\formater\Simple,
	\ephFrame\logger\adapter\File
	;

/**
 * @group Logger
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
{
	protected $filename;
	
	public function setUp()
	{
		$this->filename = __DIR__.'/../fixtures/logtest_actual.txt';
		$writer = new \ephFrame\Logger\adapter\File($this->filename);
		$writer->formater = new Simple('(:priority) :message');
		$this->fixture = new \ephFrame\Logger\Logger(
			$writer
		);
	}
	
	public function testWrite()
	{
		$this->fixture->error('this is an example error message');
		$this->assertFileExists($this->filename);
		$this->assertFileEquals($this->filename, __DIR__.'/../fixtures/logtest_actual.txt');
	}
	
	public function tearDown()
	{
		@unlink($this->filename);
	}
}