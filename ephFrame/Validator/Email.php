<?php

namespace ephFrame\Validator;

class Email extends Regexp
{
	public $unicode = false; // unicode emails are experimental
	
	public $message = 'This value is not a valid email address';
	
	public function validate($value)
	{
		if ($this->unicode) {
			$this->regexp = '/^([^@\s]+)@((?:[-\p{L}\p{N}]+\.)+[a-z]{2,})$/iu';
		} else {
			$chars = preg_quote("!#$%&'*+/=?^_`{|}~.-", '/');
			$this->regexp = '/^[a-z0-9'.$chars.']{1,}@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';
		}
		return parent::validate($value);
	}
}