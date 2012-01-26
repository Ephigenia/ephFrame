<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Form\Element\Element;

abstract class Decorator extends \ephFrame\core\Configurable
{
	public $element;
	
	public $position = Position::APPEND;
	
	public function __construct($element = null, Array $options = array())
	{
		if ($element) {
			$this->element = $element;
		}
		return parent::__construct($options);
	}
	
	public function decorate($content)
	{
		switch($this->position) {
			default:
			case Position::APPEND:
				$return = $content.$this;
				break;
			case Position::WRAP:
				$this->value = $content;
				$return = $this;
				break;
			case Position::INSERT_AFTER:
				$content->escaped = false;
				$content->value = $content->value.$this;
				$return = $content;
				break;
			case Position::INSERT_BEFORE:
				$content->escaped = false;
				$content->value = $this.$content->value;
				$return = $content;
				break;
			case Position::PREPEND:
				$return = $this.$content;
				break;
		}
		return $return;
	}
}