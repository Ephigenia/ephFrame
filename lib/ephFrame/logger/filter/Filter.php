<?php

namespace ephFrame\logger\filter;

use
	ephFrame\logger\Event
	;

interface Filter
{
	public function accept(Event $event);
}