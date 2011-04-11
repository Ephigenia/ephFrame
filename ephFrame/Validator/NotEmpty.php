<?php

namespace ephFrame\Validator;

class NotEmpty extends Validator
{
	public $message = 'This value should not be left empty.';
	
	public function validate($value)
	{
		return !empty($value);
	}
}