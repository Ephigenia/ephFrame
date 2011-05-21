<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Tag;

class Description extends Error
{
	public $attributes = array(
		'class' => 'description',
		'escaped' => true,
	);
	
	public function __toString()
	{
		if (!empty($this->element->description)) {
			return (string) new Tag($this->tag, trim($this->element->description), $this->attributes);
		}
		return '';
	}
}