<?php

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