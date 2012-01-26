<?php

namespace ephFrame\HTML\Form;

use \ephFrame\HTML\Tag;

class Fieldset extends \ArrayObject
{
	public $visible = true;
	
	public $decorators = array();
	
	public $legend = false;
	
	public $attributes = array(
		'escaped' => false,
	);
	
	public function __construct(Array $elements = array(), Array $attributes = array())
	{
		foreach(array('visible', 'legend', 'decorators') as $varname) if (isset($attributes[$varname])) {
			if (is_array($this->{$varname})) {
				$this->{$varname} += $attributes[$varname];
			} else {
				$this->{$varname} = $attributes[$varname];
			}
			unset($attributes[$varname]);
		}
		$this->attributes += $attributes;
		if ($this->decorators !== false && empty($this->decorators)) {
			$this->decorators = $this->defaultDecorators();
		}
		return parent::__construct($elements, \ArrayObject::ARRAY_AS_PROPS);
	}
	
	protected function defaultDecorators()
	{
		return array(
			'legend' => new \ephFrame\HTML\Form\Decorator\Legend($this),
		);
	}
	
	public function tag()
	{
		return new \ephFrame\HTML\Tag('fieldset', implode(PHP_EOL, (array) $this), $this->attributes);
	}
	
	public function __toString()
	{
		if (!$this->visible) {
			return '';
		}
		$rendered = $this->tag();
		foreach($this->decorators as $Decorator) {
			$rendered = $Decorator->decorate($rendered);
		}
		return (string) $rendered;
	}
}