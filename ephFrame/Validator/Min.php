<?php

namespace ephFrame\Validator;

class Min extends Validator
{
	public $min;
	
	public $message = 'This value should be :min or less';
	
	public function validate($value)
	{
		return $value > $this->min;
	}
}