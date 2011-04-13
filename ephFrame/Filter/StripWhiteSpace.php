<?php

namespace ephFrame\Filter;

class StripWhiteSpace extends Filter
{
	public function apply($value)
	{
		return preg_replace('@\s{2,}@', ' ',preg_replace('@[\r\t\n]+@', ' ', $value));
	}
}