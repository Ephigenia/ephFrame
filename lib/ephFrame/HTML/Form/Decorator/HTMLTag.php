<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Tag;

class HTMLTag extends Decorator
{
	public $tag = 'div';
	
	public $value = false;

	public $attributes = array(
		'escaped' => false,
	);
	
	public $position = Position::WRAP;
	
	public function __toString()
	{
		if (empty($this->value)) {
			return '';
		}
		return (string) new Tag($this->tag, $this->value, $this->attributes);
	}
}