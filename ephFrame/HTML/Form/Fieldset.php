<?php

namespace ephFrame\HTML\Form;

use \ephFrame\HTML\Tag;

class Fieldset extends \ArrayObject
{
	public $visible = true;
	
	public $decorators = array();
	
	public $attributes = array(
		'escaped' => false,
	);
	
	public function __construct(Array $elements = array(), Array $attributes = array())
	{
		if (isset($attributes['visible'])) {
			$this->visible = (bool) $attributes['visible'];
			unset($attributes['visible']);
		}
		$this->attributes += $attributes;
		return parent::__construct($elements, \ArrayObject::ARRAY_AS_PROPS);
	}
	
	public function tag()
	{
		return new \ephFrame\HTML\Tag('fieldset', implode(PHP_EOL, (array) $this), $this->attributes);
	}
	
	public function __toString()
	{
		if ($this->visible) {
			return (string) $this->tag();
		} else {
			return '';
		}
	}
}