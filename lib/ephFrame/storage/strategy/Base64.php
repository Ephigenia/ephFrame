<?php

namespace ephFrame\storage\strategy;

class Base64 extends \lithium\core\Object
{
	public function write($data)
	{
		return base64_encode($data);
	}

	public function read($data)
	{
		return base64_decode($data);
	}
}