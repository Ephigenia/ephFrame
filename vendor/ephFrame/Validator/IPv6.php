<?php

namespace ephFrame\Validator;

class IPv6 extends IP 
{
	public $regexp = '/^[0-9a-fA-F]{0,4}(:[0-9a-fA-F]{0,4}){1,5}((:[0-9a-fA-F]{0,4}){1,2}|:([\d\.]+))$/';
}