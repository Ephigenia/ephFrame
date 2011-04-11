<?php

namespace ephFrame\Filter;

class HTMLEntities extends Filter
{
	public $charset = 'UTF-8';
	
	public $quoting = ENT_QUOTES;
	
	public $doubleQuote = false;
	
	public function apply($value)
	{
		return htmlspecialchars((string) $value, $this->quoting, $this->charset, $this->doubleQuote);
	}
}