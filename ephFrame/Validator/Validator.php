<?php

namespace ephFrame\Validator;

use ephFrame\util\String;

class Validator
{
	public $message;
	
	public function __construct(Array $options = array())
	{
		foreach($options as $k => $v) {
			$this->{$k} = $v;
		}
	}
	
	public function message()
	{
		return String::substitute($this->message, (array) $this);
	}
	
	public function validate($value)
	{
		return true;
	}
}