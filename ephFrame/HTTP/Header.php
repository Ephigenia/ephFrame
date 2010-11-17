<?php

namespace ephFrame\lib\HTTP;

class Header
{
	public $status;
	
	public $data = array();
	
	public function __construct(Array $data = array())
	{
		$this->data = $data;
	}
	
	public function __toString()
	{
		
	}
}