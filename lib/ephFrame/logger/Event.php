<?php

namespace ephFrame\logger;

class Event
{
	public $priority;
	
	public $message;
	
	public $created;
	
	public function __construct($priority, $message)
	{
		$this->priority = $priority;
		$this->message = $message;
		$this->created = new \DateTime();
	}
	
	public function __toString()
	{
		return $this->message;
	}
}