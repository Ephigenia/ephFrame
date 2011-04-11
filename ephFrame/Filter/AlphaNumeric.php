<?php

namespace ephFrame\Filter;

class AlphaNumeric extends Alpha
{	
	public function apply($value)
	{
		$whitespace = $this->whitespace ? '\s' : '';
		$chars = preg_quote(implode('', $this->chars), '@');
		if ($this->unicode) {
			$this->regexp = '@[^\p{L}\p{N}'.$whitespace.$chars.']+@u';
		} else {
			$this->regexp = '@[^A-Za-z0-9'.$whitespace.$chars.']+@';
		}
		return preg_replace($this->regexp, $this->replace, $value);
	}
}