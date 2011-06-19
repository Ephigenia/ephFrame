<?php

namespace ephFrame\test\logger\filter;

use
	\ephFrame\logger\filter\Priority,
	\ephFrame\logger\Event,
	\ephFrame\logger\Logger
	;


/**
 * @group Logger
 * @group LoggerFilter
 */
class PriorityTest extends \PHPUnit_Framework_TestCase
{	
	public function testSimpleMatch()
	{
		$filter = new Priority(Logger::WARNING);
		$event = new Event(Logger::WARNING, 'warning', 'warning message that should be shown');
		$this->assertTrue($filter->accept($event));
		// change severity
		$event->priority = Logger::EMERGENCY;
		$this->assertTrue($filter->accept($event));
		$event->priority = Logger::DEBUG;
		$this->assertFalse($filter->accept($event));
	}
	
	public function testHigherPriorities()
	{
		$filter = new Priority(Logger::WARNING, '>');
		$event = new Event(Logger::WARNING, 'warning', 'warning message that should be shown');
		$this->assertFalse($filter->accept($event));
		$event->priority = Logger::ERROR;
		$this->assertFalse($filter->accept($event));
	}
}