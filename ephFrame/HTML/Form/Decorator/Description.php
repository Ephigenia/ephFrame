<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Tag;

class Description extends Decorator
{
	public $tag = 'p';
	
	public $position = DecoratorPosition::APPEND;
	
	public $attributes = array(
		'class' => 'description',
	);
	
	public function __toString()
	{
		if (!empty($this->element->description)) {
			return (string) new Tag($this->tag, trim($this->element->description), $this->attributes);
		}
		return '';
	}
}