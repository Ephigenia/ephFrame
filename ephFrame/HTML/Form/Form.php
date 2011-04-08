<?php

namespace ephFrame\HTML\Form;

class Form
{
	public $fieldsets = array();
	
	public $attributes = array();
	
	public function __construct(Array $attributes = array())
	{
		$this->fieldsets[] = new Fieldset();
		$this->attributes += $attributes;
		$this->configure();
	}
	
	public function tag()
	{
		return new \ephFrame\HTML\Tag('form', implode(PHP_EOL, $this->fieldsets), $this->attributes + array('escaped' => false));
	}
	
	public function __toString()
	{
		return (string) $this->tag();
	}
}
