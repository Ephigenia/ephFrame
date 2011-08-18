<?php

namespace ephFrame\HTML\Form\Element;

use
	\ephFrame\HTML\Tag
	;

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
		$options = array();
		foreach($this->options as $value => $label) {
			if (is_array($label)) {
				$options[] = $this->optGroup($value, $label);
			} else {
				$options[] = $this->optionTag($label, $value);
			}
		}
		unset($this->attributes['value']);
		return new Tag($this->tag, $options, $this->attributes + array('escaped' => false));
	}
	
	protected function optGroup($label, Array $options = array())
	{
		$optionTags = array();
		foreach($options as $value => $label) {
			$optionTags[] = $this->optionTag($label, $value);
		}
		return new Tag('optgroup', $optionTags, array('label' => $label));
	}
	
	protected function optionTag($label, $value)
	{
		$optionAttributes = array(
			'value' => $value,
		);
		if (!isset($this->data) && isset($this->attributes['value'])) {
			if ($this->attributes['value'] == $value || (is_array($this->attributes['value']) && in_array($value, $this->attributes['value']))) {
				$optionAttributes['selected'] = 'selected';
			}
		} else {
			if ($this->data == $value || (is_array($this->data) && in_array($value, $this->data))) {
				$optionAttributes['selected'] = 'selected';
			}
		}
		return new Tag('option', $label, $optionAttributes);
	}
}