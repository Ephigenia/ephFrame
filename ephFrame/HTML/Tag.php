<?php

namespace ephFrame\HTML;

use \ephFrame\HTML\Attributes;

class Tag
{
	public $name;
	
	public $value;
	
	public $attributes = array();
	
	public $escaped = true;
	
	public function __construct($name = null, $value = null, Array $attributes = array())
	{
		if (func_num_args() >= 1) {
			$this->name = trim((string) $name);
		}
		if (func_num_args() >= 2) {
			$this->value = $value;
		}
		if (isset($attributes['escaped']) && !$attributes['escaped']) {
			unset($attributes->escaped);
			$this->escaped = false;
		}
		$this->attributes = new Attributes($attributes + $this->attributes);
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
		if (empty($this->value) && !in_array($this->name, array('textarea'))) {
			return $this->openTag();
		} elseif ($this->value instanceof Tag || !$this->escaped) {
			return $this->openTag().(string) $this->value.$this->closeTag();
		} else {
			return $this->openTag().htmlspecialchars((string) $this->value, ENT_QUOTES, 'UTF-8', false).$this->closeTag();
		}
	}
}