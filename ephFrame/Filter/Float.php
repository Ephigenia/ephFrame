<?php

namespace ephFrame\Filter;

class Float extends Filter
{
	public function apply($value)
	{
		return (float) $value;
	}
}