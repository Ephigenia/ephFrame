<?php

namespace ephFrame\util;

use ephFrame\util\HTMLAttributes;

class HTMLTag
{
	public $name;
	public $value;
	public $attributes = array();
	
	public function __construct($name, $value = null, Array $attributes = array())
	{
		$this->name = $name;
		$this->value = $value;
		$this->attributes = new HTMLAttributes($attributes);
	}
	
	public function __toString()
	{
		$rendered = '<'.trim($this->name.' '.(string) $this->attributes); 
		if (empty($this->value)) {
			$rendered .= ' />';
		} else {
			$rendered = $rendered.'>'.htmlspecialchars($this->value, ENT_QUOTES, 'UTF-8', false).'</'.$this->name.'>';
		}
		return $rendered;
	}
}