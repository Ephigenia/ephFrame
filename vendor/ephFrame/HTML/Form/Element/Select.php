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
		$options = array();
		foreach($this->options as $value => $label) {
			$optionAttributes = array(
				'value' => $value,
			);
			if (!isset($this->data)) {
				if ($this->attributes['value'] == $value || (is_array($this->attributes['value']) && in_array($value, $this->attributes['value']))) {
					$optionAttributes['selected'] = 'selected';
				}
			} else {
				if ($this->data == $value || (is_array($this->data) && in_array($value, $this->data))) {
					$optionAttributes['selected'] = 'selected';
				}
			}
			$options[] = new \ephFrame\HTML\Tag('option', $label, $optionAttributes);
		}
		unset($this->attributes['value']);
		return new \ephFrame\HTML\Tag($this->tag, $options, $this->attributes + array('escaped' => false));
	}
}