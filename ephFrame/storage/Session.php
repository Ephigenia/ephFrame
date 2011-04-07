<?php

namespace ephFrame\storage;

class Session extends Adaptable
{
	protected static $options = array(
		'adapter' => 'ephFrame\storage\session\adapter\PHP',
	);
	
	public function id()
	{
		return $this->adapter()->id();
	}
}