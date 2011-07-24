<?php

namespace ephFrame\Filter;

class Float extends Filter
{
	public function apply($value)
	{
		return (float) preg_replace('@[^0-9.-]+@', '', $value);
	}
}