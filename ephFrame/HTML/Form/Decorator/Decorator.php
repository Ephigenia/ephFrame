<?php

namespace ephFrame\HTML\Form\Decorator;

class Decorator
{
	public $element;
	
	public $options = array();
	
	public function __construct($element, Array $options = array())
	{
		$this->element = $element;
		$this->options += $options;
	}
}