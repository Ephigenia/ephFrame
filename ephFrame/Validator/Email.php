<?php

namespace ephFrame\Validator;

class Email extends Regexp
{
	public $message = 'This value is not a valid email address';
	
	public $regexp = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';
}