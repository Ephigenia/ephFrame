<?php

namespace ephFrame\Validator;

use ephFrame\util\String;

class MinLength extends Validator
{
	public $length = 0;
	
	public $message = 'This value is too short. It should have :length characters or more';
	
	public function validate($value)
	{
		return String::length((string) $value) >= $this->length;
	}
}