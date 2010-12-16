<?php

namespace ephFrame\util;

use ephFrame\util\HTMLAttributes;

class HTMLTag
{
	public $name;
	
	public $value;
	
	public $attributes = array();
	
	public $escaped = true;
	
	public function __construct($name, $value = null, Array $attributes = array())
	{
		$this->name = (string) $name;
		$this->value = $value;
		if (isset($attributes['escaped']) && !$attributes['escaped']) {
			unset($attributes->escaped);
			$this->escaped = false;
		}
		$this->attributes = new HTMLAttributes($attributes);
	}
	
	public function __toString()
	{
		$rendered = '<'.trim($this->name.' '.(string) $this->attributes); 
		if (empty($this->value)) {
			$rendered .= ' />';
		} elseif ($this->value instanceof HTMLTag || !$this->escaped) {
			$rendered = $rendered.'>'.(string) $this->value.'</'.$this->name.'>';
		} else {
			$rendered = $rendered.'>'.htmlspecialchars((string) $this->value, ENT_QUOTES, 'UTF-8', false).'</'.$this->name.'>';
		}
		return $rendered;
	}
}