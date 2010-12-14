<?php

namespace ephFrame\util;

class HTMLAttributes extends \ArrayObject
{
	public function __construct(Array $array = array())
	{
		return parent::__construct(array_unique($array), \ArrayObject::ARRAY_AS_PROPS);
	}
	
	public function __toString()
	{
		$rendered = '';
		foreach($this as $key => $value) {
			$rendered .= $key.'="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false).'" ';
		}
		return trim($rendered);
	}
}