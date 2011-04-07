<?php

namespace app\component\Form\Decorator;

use \ephFrame\HTML\Tag;

class Label extends Decorator
{
	public function __toString()
	{
 		return (string) new Tag('label', $this->options['label'] ?: $this->element->attributes['name'], array('for' => $this->element->name));
	}
}