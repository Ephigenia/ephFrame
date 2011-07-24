<?php

namespace ephFrame\validator;

use ephFrame\util\String;

class MaxLength extends Validator
{
	public $limit = 0;
	
	public $message = 'This value is too long. It should have :limit characters or less';
	
	public function validate($value)
	{
		return parent::validate(String::length((string) $value) <= $this->limit);
	}
}