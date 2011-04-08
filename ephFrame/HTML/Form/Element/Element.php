<?php

namespace ephFrame\HTML\Form\Element;

use \ephFrame\HTML\Tag;

class Element
{
	public $required;
	
	public $decorators = array();
	
	protected $tag = 'input';
	
	public $description;
	
	public $label;
	
	public $data;
	
	public $attributes = array();
	
	public function __construct($name, $value = null, Array $options = array())
	{
		$this->attributes += array(
			'name' => $name,
		);
		foreach($options as $k => $v) {
			if (is_array($this->{$k}) && is_array($k)) {
				$this->{$k} += $v;
			} else {
				$this->{$k} = $v;
			}
		}
		// decorators
		if ($this->decorators !== false && empty($this->decorators)) {
			$this->decorators = array(
				'label' => new \ephFrame\HTML\Form\Decorator\Label($this),
				'description' => new \ephFrame\HTML\Form\Decorator\Description($this),
				'wrap' => new \ephFrame\HTML\Form\Decorator\HTMLTag($this),
			);
		}
	}
	
	public function tag()
	{
		$this->attributes['value'] = $this->value;
		return new \ephFrame\HTML\Tag($this->tag, null, $this->attributes);
	}
	
	public function __toString()
	{
		$rendered = $this->tag();
		if (is_array($this->decorators)) foreach($this->decorators as $decorator) {
			$rendered = $decorator->decorate($rendered);
		}
		return (string) $rendered;
	}
}