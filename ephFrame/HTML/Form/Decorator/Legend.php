<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Tag;

class Legend extends Decorator
{
	public $attributes = array();
	
	public $position = DecoratorPosition::PREPEND;
	
	public function decorate($tag)
	{
		$tag->value = $this.$tag->value;
		return (string) $tag;
	}
	
	public function __toString()
	{
		if (!empty($this->element->legend)) {
			return (string) new Tag('legend', $this->element->legend, $this->attributes);
		} else {
			return '';
		}
	}
}