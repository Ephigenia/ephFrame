<?php

namespace ephFrame\Filter;

class Trim extends Filter
{
	public $chars = array();
	
	public function apply($value)
	{
		if (!empty($this->chars)) {
			return trim((string) $value, is_array($this->chars) ? implode('', $this->chars) : $this->chars);
		}
		return trim($value);
	}
}