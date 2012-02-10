<?php

namespace ephFrame\validator;

class Max extends Validator
{
	public $limit;
	
	public $message = 'This value should be :limit or less';
	
	public function validate($value)
	{
		return $value <= $this->limit;
	}
}