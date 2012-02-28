<?php

namespace ephFrame\HTML\Form\Decorator;

use \ephFrame\HTML\Tag;

class HTMLTag extends Decorator
{
	/**
	 * name of the sgml tag that should be used for wrapping
	 * @var string
	 */
	public $tag = 'div';
	
	public $value = false;
	
	/**
	 * Should a lowered class name of the connected element be used as default?
	 * @var bool
	 */
	public $addElementClass = true;

	/**
	 * List of attributes that are used during rendering
	 * @var array(string)
	 */
	public $attributes = array(
		'escaped' => false,
		'class' => array(), 
	);
	
	public $position = Position::WRAP;

	public function __toString()
	{
		if ($this->addElementClass) {
			$elementClass = strtolower(substr(strrchr(get_class($this->element), '\\'), 1));
			if (is_string($this->attributes['class'])) {
				$this->attributes['class'] = array($this->attributes['class']);
			}
			if (!in_array($elementClass, $this->attributes['class'])) {
				$this->attributes['class'][] = $elementClass;
			}
		}
		if (empty($this->value)) {
			return '';
		}
		return (string) new Tag($this->tag, $this->value, $this->attributes);
	}
}