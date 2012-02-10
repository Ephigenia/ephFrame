<?php

namespace ephFrame\validator;

class Blank extends Validator
{
	public $message = 'This value should be blank';
	
	public function validate($value)
	{
		return $value === null || $value === '';
	}
}