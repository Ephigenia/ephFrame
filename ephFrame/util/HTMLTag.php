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
		$this->name = trim((string) $name);
		$this->value = $value;
		if (isset($attributes['escaped']) && !$attributes['escaped']) {
			unset($attributes->escaped);
			$this->escaped = false;
		}
		$this->attributes = new HTMLAttributes($attributes);
	}
	
	public function openTag()
	{
		if (empty($this->value)) {
			return '<'.trim($this->name.' '.(string) $this->attributes).' />';
		} else {
			return '<'.trim($this->name.' '.(string) $this->attributes).'>';
		}
	}
	
	public function closeTag()
	{
		return '</'.$this->name.'>';
	}
	
	public function __toString()
	{
		if (empty($this->value)) {
			return $this->openTag();
		} elseif ($this->value instanceof HTMLTag || !$this->escaped) {
			return $this->openTag().(string) $this->value.$this->closeTag();
		} else {
			return $this->openTag().htmlspecialchars((string) $this->value, ENT_QUOTES, 'UTF-8', false).$this->closeTag();
		}
	}
}