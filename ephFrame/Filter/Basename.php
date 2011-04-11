<?php

namespace ephFrame\Filter;

class Basename extends Filter
{
	public function apply($value)
	{
		return basename((string) $value);
	}
}