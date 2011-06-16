<?php

namespace ephFrame\Validator;

class Locale extends Regexp
{
	public $regexp = '^[a-z]{2}_[a-z]{2}$';
	
	public $message = 'This value does is not a valid locale.';
}