<?php

namespace ephFrame\Filter;

class NormalizeLineBrakes extends PregReplace
{
	public $regexp = '!(\r\n|\n\r|\r)!';
	
	public $replace = PHP_EOL;
}