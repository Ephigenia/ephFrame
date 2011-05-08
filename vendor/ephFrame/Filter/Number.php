<?php

namespace ephFrame\Filter;

/**
 * 
 */
class Number extends PregReplace
{
	public $unicode = true;
	
	public $whitespace = false;
	
	public function apply($value)
	{
		$whitespace = $this->whitespace ? '\s' : '';
		if ($this->unicode) {
			$this->regexp = '@[^\p{N}.'.$whitespace.']+@u';
		} else {
			$this->regexp = '@[^0-9.'.$whitespace.']+@';
		}
		return parent::apply($value);
	}
}