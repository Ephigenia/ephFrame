<?php

namespace ephFrame\HTML\Form\Element;

class Number extends Element
{
	public $attributes = array(
		'type' => 'number',
		'step' => 1,
		'min' => 0,
	);
	
	protected function defaultValidators()
	{
		return array(
			new \ephFrame\validator\Number(),
		);
	}
	
	protected function defaultFilters()
	{
		return array(
			new \ephFrame\Filter\Number(array(
				'unicode' => true,
				'whitespace' => false,
			)),
		);
	}
}