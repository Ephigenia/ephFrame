<?php

namespace ephFrame\HTML\Form\Element;

class Submit extends Button
{
	public $attributes = array(
		'type' => 'submit',
	);
	
	public function tag()
	{
		if (empty($this->attributes['value'])) {
			$this->attributes['value'] = $this->label;
		}
		return new \ephFrame\HTML\Tag($this->tag, null, $this->attributes);
	}
}