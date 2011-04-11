<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Tag;

class Label extends Decorator
{
	public $attributes = array();
	
	public $position = DecoratorPosition::PREPEND;
	
	public function __toString()
	{
		if (empty($this->attributes['for'])) {
			$this->attributes['for'] = $this->element->attributes['name'];
		}
		$label = false;
		if (!empty($this->element->label)) {
			$label = $this->element->label;
		} elseif ($this->element->label !== false) {
			$label = $this->element->attributes['name'];
		}
 		return (string) new Tag('label', $label, $this->attributes);
	}
}