<?php

namespace ephFrame\util;

class Timer
{
	public $start;
	
	public $elapsed = 0.0;
	
	public $steps = array();
	
	public $end;
	
	public function __construct()
	{
		$this->start = microtime(true);
	}
	
	public function step($name = null)
	{
		$this->elapsed = microtime(true) - $this->start;
		if ($name) {
			$this->steps[$name] = $this->elapsed;
		} else {
			array_push($this->steps,  $this->elapsed);
		}
		return $this;
	}
	
	public function stop()
	{
		$this->end = microtime(true);
		$this->step();
		return $this;
	}
	
	public function __toString()
	{
		return (string) $this->elapsed;
	}
}