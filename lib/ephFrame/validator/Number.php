<?php

namespace ephFrame\validator;

class Number extends Regexp
{
	public $unicode = true;
	
	public $whitespace = true;
	
	public $message = 'This value is not a valid number';
	
	public function validate($value)
	{
		$whitespace = $this->whitespace ? '\s' : '';
		if ($this->unicode) {
			$this->regexp = '@^[\p{N}.'.$whitespace.']+$@u';
		} else {
			$this->regexp = '@^[0-9.'.$whitespace.']+$@';
		}
		return parent::validate($value);
	}	
}