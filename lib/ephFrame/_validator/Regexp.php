<?php

namespace ephFrame\validator;

class Regexp extends Validator
{
	public $regexp;
	
	public $message = 'This value does not match the required format';
	
	public function validate($value)
	{
		return (bool) preg_match($this->regexp, (string) $value);
	}
}