<?php

namespace ephFrame\test\logger\filter;

use
	\ephFrame\logger\filter\Message,
	\ephFrame\logger\Event
	;


/**
 * @group Logger
 * @group LoggerFilter
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{	
	public function testSimpleAccept()
	{
		$filter = new Message($regexp = '@testmessage@');
		$event = new Event(1, 'alert', 'this is just a testmessage');
		$this->assertTrue($filter->accept($event));
	}
}