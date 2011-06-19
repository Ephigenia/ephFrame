<?php

namespace ephFrame\logger;

class Event
{
	public $priority;
	
	public $priorityName;
	
	public $message;
	
	public $created;
	
	public function __construct($priority, $priorityName, $message)
	{
		$this->priority = $priority;
		$this->priorityName = $priorityName;
		$this->message = $message;
		$this->created = new \DateTime();
	}
	
	public function __toString()
	{
		return $this->message;
	}
}