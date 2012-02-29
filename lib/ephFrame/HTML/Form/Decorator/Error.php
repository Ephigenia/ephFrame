<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Tag;

class Error extends Decorator
{
	public $attributes = array(
		'class' => 'error',
		'escaped' => false,
	);
	
	public $tag = 'p';
	
	public $glue = PHP_EOL;
	
	public $position = Position::APPEND;
	
	public function __toString()
	{
		if (!$this->element->error()) {
			return '';
		}
 		return (string) new Tag($this->tag, implode($this->glue, $this->element->errors), $this->attributes);
	}
}