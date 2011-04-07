<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Tag;

class Description extends Decorator
{
	public $options = array(
		'tag' => 'p',
		'attributes' => array(
			'class' => 'description',
		),
	);
	
	public function __toString()
	{
		if (!empty($this->element->description)) {
			return (string) new Tag($this->options['tag'], trim($this->element->description), $this->options['attributes']);
		}
		return '';
	}
}