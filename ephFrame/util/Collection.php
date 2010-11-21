<?php

namespace ephFrame\util;

class Collection extends \ArrayObject
{
	public function __construct(Array $array = array())
	{
		return parent::__construct(array_unique($array), \ArrayObject::ARRAY_AS_PROPS);
	}
	
	public function offsetSet($k, $value)
	{
		parent::offsetSet($k, $value);
		$this->exchangeArray(array_unique((array) $this));
	}
}