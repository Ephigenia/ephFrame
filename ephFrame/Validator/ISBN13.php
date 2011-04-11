<?php

namespace ephFrame\Validator;

class ISBN13 extends Regexp
{
	public $message = 'This value is no valid ISBN13 number';
	
	public $regexp = 'ISBN\x20(?=.{13}$)\d{1,5}([- ])\d{1,7}\1\d{1,6}\1(\d|X)$';
}