<?php

namespace ephFrame\Filter;

class Boolean extends Filter
{
	public function apply($value)
	{
		return (bool) $value;
	}
}