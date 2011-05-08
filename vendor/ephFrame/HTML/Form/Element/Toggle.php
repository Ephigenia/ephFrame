<?php

namespace ephFrame\HTML\Form\Element;

class Toggle extends Element
{
	public $attributes = array(
		'value' => true,
	);
	
	public function tag()
	{
		$this->attributes['checked'] = ((bool)$this->value) ? 'checked' : null;
		return parent::tag();
	}
}