<?php

namespace ephFrame\Filter;

class Alpha extends PregReplace
{
	public $unicode = true;
	
	public $whitespace = true;
	
	public $chars = array();
	
	public function apply($value)
	{
		$whitespace = $this->whitespace ? '\s' : '';
		$chars = preg_quote(implode('', $this->chars), '@');
		if ($this->unicode) {
			$this->regexp = '@[^\p{L}'.$whitespace.$chars.']+@u';
		} else {
			$this->regexp = '@[^A-Za-z'.$whitespace.$chars.']+@';
		}
		return parent::apply($value);
	}
}