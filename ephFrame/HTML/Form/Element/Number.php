<?php

namespace ephFrame\HTML\Form\Element;

class Number extends Element
{
	public $attributes = array(
		'type' => 'number',
	);
	
	protected function defaultValidators()
	{
		return array(
			new \ephFrame\Validator\Integer(),
		);
	}
}