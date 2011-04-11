<?php

namespace ephFrame\Filter;

class Alpha extends PregRepllace
{
	public $unicode = true;
	
	public $whitespace = true;
	
	public function apply($value)
	{
		$whitespace = $this->whitespace ? '\s' : '';
		if ($this->unicode) {
			$this->regexp = '@[^\p{L}'.$whitespace.']+@u';
		} else {
			$this->regexp = '@[^A-Za-z'.$whitespace.']+@';
		}
		return parent::apply($value);
	}
}