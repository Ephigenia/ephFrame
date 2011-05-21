<?php

namespace ephFrame\HTML\Form\Element;

class Toggle extends Element
{
	public $attributes = array(
		'value' => true,
	);
	
	public function tag()
	{
		$this->attributes['checked'] = ((bool) $this->data) ? 'checked' : null;
		return parent::tag();
	}
	
	protected function defaultDecorators()
	{
		return array(
			'label' => new \ephFrame\HTML\Form\Decorator\Label($this, array(
				'position' => \ephFrame\HTML\Form\Decorator\Position::APPEND,
				'format' => '%s',
			)),
			'description' => new \ephFrame\HTML\Form\Decorator\Description($this),
			'error' => new \ephFrame\HTML\Form\Decorator\Error($this),
			'wrap' => new \ephFrame\HTML\Form\Decorator\HTMLTag($this),
		);
	}
}