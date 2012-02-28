<?php

namespace ephFrame\test\logger\filter;

use
	\ephFrame\logger\filter\Message,
	\ephFrame\logger\Event
	;


/**
 * @group Logger
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{	
	public function testSimpleAccept()
	{
		$filter = new Message('@testmessage@');
		$event = new Event(1, 'this is just a testmessage');
		$this->assertTrue($filter->accept($event));
	}
	
	public function testSimpleIgnore()
	{
		$filter = new Message('@testmessage@');
		$event = new Event(1, 'this is not a match');
		$this->assertFalse($filter->accept($event));
	}
}