<?php

namespace ephFrame\Filter;

class Filter
{
	public function __construct(Array $options = array())
	{
		foreach($options as $k => $v) {
			$this->{$k} = $v;
		}
	}
	
	public function apply($value)
	{
		return $value;
	}
}