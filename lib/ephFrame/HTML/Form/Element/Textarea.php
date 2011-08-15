<?php

namespace ephFrame\HTML\Form\Element;

class Textarea extends Element
{
	protected $tag = 'textarea';
	
	public function tag()
	{
		return new \ephFrame\HTML\Tag($this->tag, $this->data, $this->attributes);
	}
	
	protected function defaultFilters()
	{
		return array(
			'Trim' => new \ephFrame\Filter\Trim(),
			'StripWhiteSpace' => new \ephFrame\Filter\StripWhiteSpace(),
		);
	}
}