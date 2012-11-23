<?php

namespace ephFrame\HTML\Form\Element;

class Textarea extends Element
{
	protected $tag = 'textarea';

	public function __construct($name = null, $value = null, Array $options = array())
	{
		parent::__construct($name, null, $options);
		if ($value !== null) {
			$this->data = $value;
		}
	}
	
	public function tag()
	{
		return new \ephFrame\HTML\Tag($this->tag, $this->data, $this->attributes);
	}
	
	protected function defaultFilters()
	{
		return array(
			'Trim' => new \ephFrame\Filter\Trim(),
		);
	}
}