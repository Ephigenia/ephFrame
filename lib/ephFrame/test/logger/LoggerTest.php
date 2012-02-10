<?php

namespace ephFrame\test\logger;

use
	\ephFrame\logger\Logger,
	\ephFrame\logger\formater\Simple,
	\ephFrame\logger\adapter\File,
	\ephFrame\logger\filter\Priority
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
		$writer = new \ephFrame\logger\adapter\File($this->filename);
		$writer->formater = new Simple('(:priority) :message');
		$this->fixture = new \ephFrame\logger\Logger(
			$writer
		);
	}
	
	public function testWrite()
	{
		$this->fixture->error('this is an example error message');
		$this->assertFileExists($this->filename);
		$this->assertFileEquals($this->filename, __DIR__.'/../fixtures/logtest_actual.txt');
	}
	
	public function testIgnoredWrite()
	{
		$this->fixture->filters[] = new Priority(Logger::WARNING);
		$this->assertFalse($this->fixture->write(Logger::NOTICE, 'warning message that should be shown'));
	}
	
	public function tearDown()
	{
		@unlink($this->filename);
	}
}