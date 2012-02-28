<?php

namespace ephFrame\test\logger;

use
	\ephFrame\logger\Event,
	\ephFrame\logger\Logger
	;

/**
 * @group Logger
 */
class EventTest extends \PHPUnit_Framework_TestCase
{
	protected $filename;
	
	public function testWrite()
	{
		$Event = new Event(Logger::EMERGENCY, 'message content');
		$this->assertEquals('message content', (string) $Event);
	}
}