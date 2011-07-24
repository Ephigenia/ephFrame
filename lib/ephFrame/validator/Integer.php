<?php

namespace ephFrame\validator;

class Integer extends Regexp
{
	public $message = 'This value is not a valid integer';
	
	public $regexp = '/[0-9]+/i';
}