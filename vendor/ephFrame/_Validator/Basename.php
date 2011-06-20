<?php

namespace ephFrame\Validator;

class Basename extends Regexp
{
	public $regexp = '@[^/?*:;{}\\]+@';
}