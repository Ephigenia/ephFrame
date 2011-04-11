<?php

namespace ephFrame\Validator;

use ephFrame\util\String;

class MaxLength extends Validator
{
	public $length = 0;
	
	public $message = 'This value is too long. It should have :length characters or less';
	
	public function validate($value)
	{
		return String::length((string) $value <= $this->length;
	}
}