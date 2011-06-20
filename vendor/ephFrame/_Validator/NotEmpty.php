<?php

namespace ephFrame\Validator;

class NotEmpty extends Validator
{
	public $message = 'This value should not be left empty.';
	
	public function validate($value)
	{
		if (is_bool($value) && $value == false) {
			return false;
		}
		$value = preg_replace('@\s+@', '', $value);
		return !empty($value);
	}
}