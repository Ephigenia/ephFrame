<?php

namespace ephFrame\HTML\Form\Element;

class Submit extends Button
{
	public $attributes = array(
		'type' => 'submit',
	);
	
	public function tag()
	{
		$this->attributes['value'] = $this->label;
		return new \ephFrame\HTML\Tag($this->tag, null, $this->attributes);
	}
}