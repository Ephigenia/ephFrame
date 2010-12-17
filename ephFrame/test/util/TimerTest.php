<?php

namespace ephFrame\test\util;

use ephFrame\util\Timer;

class TimerTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->timer = new Timer();
	}
	
	public function testStep()
	{
		$this->timer->step();
		$elapsed1 = $this->timer->elapsed;
		$this->assertTrue($elapsed1 > 0.0001);
		$this->timer->step();
		$this->assertTrue($this->timer->elapsed > $elapsed1);
		$this->assertTrue(count($this->timer->steps) == 2);
	}
	
	public function testStop()
	{
		$this->timer->stop();
		$this->assertTrue($this->timer->elapsed > 0.);
		$this->assertTrue($this->timer->end > 0.);
	}
	
	public function test__toString()
	{
		$this->assertStringMatchesFormat('%f', (string) $this->timer);
	}
}