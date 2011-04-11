<?php

namespace ephFrame\Validator;

class Max extends Validator
{
	public $max;
	
	public $message = 'This value should be :mx or less';
	
	public function validate($value)
	{
		return $value < $this->max;
	}
}