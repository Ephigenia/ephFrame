<?php

namespace ephFrame\Validator;

class Time extends Regexp
{
	public $message = "This value is not a valid time";
	
	public $regexp = '/(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/';
}