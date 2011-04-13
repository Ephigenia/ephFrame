<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Tag;

class Error extends Decorator
{
	public $attributes = array(
		'class' => 'error',
	);
	
	public $tag = 'p';
	
	public $position = DecoratorPosition::APPEND;
	
	public function __toString()
	{
		if (!$this->element->error()) {
			return '';
		}
 		return (string) new Tag($this->tag, implode(PHP_EOL, $this->element->errors), $this->attributes);
	}
}