<?php

namespace ephFrame\HTML\Form\Element;

class Textarea extends Element
{
	protected $tag = 'textarea';
	
	public function tag()
	{
		return new \ephFrame\HTML\Tag($this->tag, $this->data, $this->attributes);
	}
}