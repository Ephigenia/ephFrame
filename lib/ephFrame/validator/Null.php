<?php

namespace ephFrame\validator;

class Null extends Validator
{
	public $message = 'This value should not be null.';
	
	public function validate($value)
	{
		return $value === null;
	}
}