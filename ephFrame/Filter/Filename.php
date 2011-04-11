<?php

namespace ephFrame\Filter;

use \ephFrame\util\String;

class Filename extends Filter
{
	public $maxLength = 255;
	
	public function apply($value)
	{
		$filename = trim(basename($string, '.')), '_');
		if (($dotPos = strpos($filename, '.')) !== false) {
			$extension = substr($filename, $dotPos + 1);
			$basename  = substr($filename, 0, $dotPos);
			return substr($basename, 0, 230 - strlen($extension) - 1).'.'.$extension;
		}
		return String::substr($filename, 0, $this->maxLength);
	}
}