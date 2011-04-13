<?php

namespace ephFrame\Filter;

abstract class Filter
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
	
	public function __invoke($value)
	{
		return $this->apply($value);
	}
}