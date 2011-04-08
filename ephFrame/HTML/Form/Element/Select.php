<?php

namespace ephFrame\HTML\Form\Element;

class Select extends MultipleChoice
{
	public $attributes = array(
		'multiple' => false,
		'size' => 1,
	);
	
	public function tag()
	{
		if (isset($this->attributes['multiple']) && $this->attributes['multiple']) {
			$this->attributes['multiple'] = 'multiple';
		}
		foreach($this->options as $value => $label) {
			$attributes = array(
				'value' => $value,
			);
			if ($this->data == $value || (is_array($this->data) && in_array($value, $this->data))) {
				$attributes['selected'] = 'selected';
			}
			$options[] = new \ephFrame\HTML\Tag('option', $label, $attributes);
		}
		return new \ephFrame\HTML\Tag($this->tag, $options, $this->attributes + array('escaped' => false));
	}
}