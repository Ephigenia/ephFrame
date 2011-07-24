<?php

namespace ephFrame\logger\filter;

use
	ephFrame\logger\Event
	;

abstract class Filter
{
	public function accept(Event $event)
	{
		return true;
	}
}