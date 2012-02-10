<?php

namespace ephFrame\validator;

class Email extends Regexp
{
	public $unicode = false; // unicode emails are experimental
	
	public $message = 'This value is not a valid email address';
	
	public function validate($value)
	{
		if ($this->unicode) {
			$this->regexp = '/^([^@\s]+)@((?:[-\p{L}\p{N}]+\.)+[a-z]{2,})$/iu';
		} else {
			$this->regexp = '/^[a-z0-9'.preg_quote("!#$%&'*+/=?^_`{|}~.-", '/').']{1,}@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';
		}
		return parent::validate($value);
	}
}