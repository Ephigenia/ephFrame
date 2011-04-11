<?php

namespace ephFrame\HTML\Form\Element;

class URL extends Element
{
	public $attributes = array(
		'type' => 'url',
		'maxlength' => 255,
	);
	
	protected function defaultValidators()
	{
		return array(
			new \ephFrame\Validator\URL(),
		);
	}
}