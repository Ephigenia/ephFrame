<?php

namespace ephFrame\HTML\Form\Element;

class Email extends Element
{
	public $attributes = array(
		'type' => 'email',
		'maxlength' => 255,
	);
	
	protected function defaultValidators()
	{
		return array(
			new \ephFrame\Validator\Email(),
		);
	}
}