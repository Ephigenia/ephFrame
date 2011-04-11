<?php

class NotBlank extends Blank
{
	public $message = 'This value should not be left blank.';
	
	public function validate($value)
	{
		return !parent::validate($value);
	}
}