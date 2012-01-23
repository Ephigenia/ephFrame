<?php

namespace ephFrame\storage\strategy;

class Json extends \lithium\core\Object
{
	public function write($data)
	{
		return json_encode($data);
	}

	public function read($data)
	{
		return json_decode($data, true);
	}
}