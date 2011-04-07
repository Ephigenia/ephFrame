<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Tag;

class HTMLTag extends Decorator
{
	public $options = array(
		'tag' => 'div',
		'value' => false,
		'attributes' => array(
			'escaped' => false,
		),
	);
	
	public function __toString()
	{
		return (string) new Tag($this->options['tag'], $this->options['value'], $this->options['attributes']);
	}
}