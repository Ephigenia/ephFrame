<?php

class View
{
	protected $data = array();
	
	public function __construct($template, Array $data = array())
	{
		$this->data = $data;
	}
	
	public function render()
	{
		
	}
}