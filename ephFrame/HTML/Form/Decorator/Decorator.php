<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Form\Element\Element;

abstract class Decorator
{
	public $element;
	
	public $position = DecoratorPosition::APPEND;
	
	public function __construct($element = null, Array $options = array())
	{
		if ($element) {
			$this->element = $element;
		}
		foreach($options as $k => $v) {
			if (is_array($this->{$k})) {
				$this->{$k} += $v;
			} else {
				$this->{$k} = $v;
			}
		}
	}
	
	public function decorate($content)
	{
		switch($this->position) {
			default:
			case DecoratorPosition::APPEND:
				return $content.$this;
			case DecoratorPosition::WRAP:
				$this->value = $content;
				return $this;
			case DecoratorPosition::PREPEND:
				return $this.$content;
		}
	}
}