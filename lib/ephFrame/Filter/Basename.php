<?php

namespace ephFrame\Filter;

use \ephFrame\util\String;

class Basename extends Filter
{
	public $maxLength = 255;
	
	public $paranoid = false;
	
	public function apply($value)
	{
		$result = basename((string) $value);
		$result = preg_replace('@[/?*:;{}\\\]@', '', $result);
		if ($this->paranoid) {
			$result = preg_replace('@[^A-Z0-9_.-]@i', '', $result);
		}
		if (String::length($result) > $this->maxLength) {
			if (($dotPos = strpos($result, '.')) !== false) {
				$extension = substr($result, $dotPos + 1);
				$basename  = substr($result, 0, $dotPos);
				$result = String::substr($basename, 0, $this->maxLength - String::length($extension) - 1).'.'.$extension;
			} else {
				$result = String::substr($result, 0, $this->maxLength);
			}
		}
		return $result;
	}
}