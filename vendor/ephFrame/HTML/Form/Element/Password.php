<?php

namespace ephFrame\HTML\Form\Element;

class Password extends Element
{
	public $attributes = array(
		'type' => 'password',
	);
	
	protected function defaultValidators()
	{
		return array(
			new \ephFrame\Validator\NotBlank(),
		);
	}
}