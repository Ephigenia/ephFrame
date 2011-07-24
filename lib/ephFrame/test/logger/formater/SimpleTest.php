<?php

namespace ephFrame\test\logger\Formater;

use
	\ephFrame\logger\Logger,
	\ephFrame\logger\Event,
	\ephFrame\logger\formater\Simple
	;

/**
 * @group Logger
 * @group LoggerFormater
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
{
	public function testFormat()
	{
		$Formater = new Simple(':date :message :priority');
		$Event = new Event(Logger::WARNING, 'warning', 'testmessage content');
		$this->assertEquals(
			$Formater->format($Event),
			date('Y-m-d').' testmessage content 4'
		);
	}
}