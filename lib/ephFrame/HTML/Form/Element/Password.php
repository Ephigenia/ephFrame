<?php

namespace ephFrame\HTML\Form\Element;

class Password extends Element
{
	public $attributes = array(
		'type' => 'password',
	);
	
	public $alwaysEmpty = true;
	
	protected function defaultValidators()
	{
		return array(
			new \ephFrame\validator\NotBlank(),
		);
	}
	
	public function tag()
	{
		if (!$this->alwaysEmpty) {
			return parent::tag();
		}
		return new \ephFrame\HTML\Tag($this->tag, null, $this->attributes);
	}
}