<?php

namespace ephFrame\Filter;

class StripNewlines extends PregReplace
{
	public $regexp = '@[\r\n]+@';
	
	public $replace = '';
}