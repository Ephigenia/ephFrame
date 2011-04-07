<?php

namespace ephFrame\HTML;

class Attributes extends \ArrayObject
{
	public static $renderEmptyAttributeValues = false;
	
	public static $trimAttributeValues = true;
	
	public function __construct(Array $array = array())
	{
		return parent::__construct(array_unique($array), \ArrayObject::ARRAY_AS_PROPS);
	}
	
	public function __toString()
	{
		$rendered = '';
		foreach($this as $key => $value) {
			if (is_array($value)) {
				$value = implode(' ', array_unique($value));
			}
			if (HTMLAttributes::$trimAttributeValues) {
				$value = trim($value);
			}
			if (empty($value) && !HTMLAttributes::$renderEmptyAttributeValues) {
				continue;
			}
			$rendered .= $key.'="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false).'" ';
		}
		return trim($rendered);
	}
}