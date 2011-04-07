<?php

namespace ephFrame\HTML\Form\Element;

use \ephFrame\HTML\Tag;

class Element extends Tag
{
	public $required;
	
	public $decorators = array();
	
	public $name = 'input';
	
	public $description;
	
	public $label;
	
	public function __construct($name, $value = null, Array $options = array())
	{
		$options = array('attributes' => array(
			'name' => $name,
			'value' => $value,
		)) + $options;
		parent::__construct($this->name, false, $options['attributes']);
		if ($this->decorators !== false) {
			$this->decorators = array(
				'label' => new \ephFrame\HTML\Form\Decorator\Label($this),
				'description' => new \ephFrame\HTML\Form\Decorator\Description($this),
				'wrap' => new \ephFrame\HTML\Form\Decorator\HTMLTag($this),
			);
		}
		foreach($options as $k => $v) {
			if (property_exists($this, $k) && $k !== 'attributes') $this->{$k} = $v;
		}
	}
	
	public function __toString()
	{
		$rendered = parent::__toString();
		if (is_array($this->decorators)) foreach($this->decorators as $decorator) {
			$rendered = $decorator->decorate($rendered);
		}
		return (string) $rendered;
	}
}