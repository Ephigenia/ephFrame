<?php

namespace ephFrame\logger\formater;

use
	ephFrame\logger\Event
	;

class Simple
{
	protected $format = ":timestamp :priority :message\n";
	
	public function __construct($format = null)
	{
		$this->format = (string) ($format ?: $this->format);
	}
	
	public function format(Event $Event)
	{
		return \ephFrame\util\String::substitute($this->format, array(
			'priority' => $Event->priority,
			'timestamp' => $Event->created->getTimeStamp(),
			'date' => $Event->created->format('Y-m-d'),
			'time' => $Event->created->format('h:m:s'),
			'message' => (string) $Event->message,
		));
	}
}