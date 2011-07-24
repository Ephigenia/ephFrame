<?php

namespace ephFrame\logger\adapter;

abstract class Adapter
{
	public $formater;
	
	public function __construct()
	{
		$this->formater = new \ephFrame\logger\formater\Simple();
	}
}