<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Tag;

class Label extends Decorator
{
	public $attributes = array();
	
	public $position = Position::PREPEND;
	
	public $format = '%s:';
	
	public function __toString()
	{
		if (empty($this->attributes['for']) && isset($this->element->attributes['id'])) {
			$this->attributes['for'] = $this->element->attributes['id'];
		}
		$label = false;
		if ($this->element->label) {
			$label = $this->element->label;
		} elseif ($this->element->label !== false) {
			$label = $this->element->attributes['name'];
		} else {
			return '';
		}
 		return (string) new Tag('label', sprintf($this->format, $label), $this->attributes);
	}
}