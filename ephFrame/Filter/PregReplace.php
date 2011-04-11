<?php

class PregReplace extends Filter
{
	public $regexp;
	
	public $replace;
	
	public function apply($value)
	{
		return preg_replace($this->regexp, $this->replace, $value);
	}
}