<?php

namespace ephFrame\validator;

class Basename extends Regexp
{
	public $regexp = '@[^/?*:;{}\\]+@';
}