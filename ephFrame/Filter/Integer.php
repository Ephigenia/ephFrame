<?php

namespace ephFrame\Filter;

class Integer extends Filter
{
	public function apply($value)
	{
		return (int) ((string) $value)
	}
}