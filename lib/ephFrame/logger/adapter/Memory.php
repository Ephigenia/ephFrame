<?php

namespace ephFrame\logger\adapter;

use 
	\ephFrame\logger\Event
	;

class File extends Adapter implements \Countable, \ArrayAccess
{
	protected $fp;
	
	protected $filename;
	
	protected $events = array();
		
	public function write(Event $event)
	{
		$this->events[] = $event;
		return true;
	}
	
	public function count()
	{
		return count($this->events);
	}
}