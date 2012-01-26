<?php

namespace ephFrame\logger\filter;

use
	ephFrame\logger\Event
	;

class Priority implements Filter
{
	protected $priority;
	
	protected $operator = '<=';
	
	public function __construct($priority, $operator = null)
	{
		$this->priority = (int) $priority;
		if ($operator) {
			$this->operator = $operator;
		}
	}
	
	public function accept(Event $event)
	{
		return version_compare($event->priority, $this->priority, $this->operator);
	}
}