<?php

namespace ephFrame\Filter;

class AlphaNumeric extends PregRepllace
{
	public $unicode = true;
	
	public $whitespace = true;
	
	public function apply($value)
	{
		$whitespace = $this->whitespace ? '\s' : '';
		if ($this->unicode) {
			$this->regexp = '@[^\p{L}\p{N}'.$whitespace.']+@u';
		} else {
			$this->regexp = '@[^A-Za-z0-9'.$whitespace.']+@';
		}
		return parent::apply($value);
	}
}