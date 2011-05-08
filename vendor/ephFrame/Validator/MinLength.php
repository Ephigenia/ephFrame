<?php

namespace ephFrame\Validator;

use ephFrame\util\String;

class MinLength extends Validator
{
	public $limit = 0;
	
	public $message = 'This value is too short. It should have :limit characters or more';
	
	public function validate($value)
	{
		return String::length((string) $value) >= $this->limit;
	}
}