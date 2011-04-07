<?php

namespace ephFrame\HTML\Form;

class Form
{
	protected $elements = array();
	
	public function __toString()
	{
		return implode(PHP_EOL, $this->elements);
	}
	
	public function add(\ephFrame\HTML\Form\Element\Element $element)
	{
		$this->elements[] = $element;
	}
}
