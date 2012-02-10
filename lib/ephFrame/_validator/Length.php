<?php

namespace ephFrame\validator;

use ephFrame\util\String;

class Length extends Validator
{
	public $length = 0;
	
	public $message = 'This value should be exact :length characters in length.';
	
	public function validate($value)
	{
		return parent::validate(String::length((string) $value) == $this->length);
	}
}