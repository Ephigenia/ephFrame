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
			$this->attributes['for'] = $this->element->name;
		}
 		return (string) new Tag('label', @$this->element->label ?: $this->element->attributes['name'], $this->attributes);
	}
}