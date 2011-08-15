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
		if (isset($attributes['escaped'])) {
			$this->escaped = (bool) $attributes['escaped'];
			unset($attributes['escaped']);
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
		if (empty($this->value) && !in_array($this->name, array('textarea', 'select'))) {
			return $this->openTag();
		} else {	
			if (is_array($this->value)) {
				$value = implode('', $this->value);
			} else {
				$value = $this->value;
				if ($this->escaped && !$this->value instanceOf Tag) {
					$value = nl2br(htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8', false));
				}
			}
			return $this->openTag().$value.$this->closeTag();
		}
	}
}