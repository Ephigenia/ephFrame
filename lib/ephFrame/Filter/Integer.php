<?php

namespace ephFrame\Filter;

class Integer extends Filter
{
	public function apply($value)
	{
		return (int) (preg_replace('@[^0-9.-]@', '', (string) $value));
	}
}