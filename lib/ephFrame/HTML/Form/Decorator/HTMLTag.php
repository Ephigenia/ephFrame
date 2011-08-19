<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Tag;

class HTMLTag extends Decorator
{
	public $tag = 'div';
	
	public $value = false;
	
	public $addElementClass = true;

	public $attributes = array(
		'escaped' => false,
		'class' => array(), 
	);
	
	public $position = Position::WRAP;

	public function __toString()
	{
		if ($this->addElementClass) {
			$ElementClass = substr(strrchr(get_class($this->element), '\\'), 1);
			if (!in_array($ElementClass, $this->attributes)) {
				$this->attributes['class'] = $ElementClass;
			}
		}
		if (empty($this->value)) {
			return '';
		}
		return (string) new Tag($this->tag, $this->value, $this->attributes);
	}
}