<?php

namespace ephFrame\validator;

use 
	ephFrame\util\String,
	ephFrame\core\Configurable
;

abstract class Validator extends Configurable
{
	public $message;
	
	public function message()
	{
		return String::substitute($this->message, (array) $this);
	}
	
	public function validate($value)
	{
		return (bool) $value;
	}
}