<?php

namespace ephFrame\HTML\Form\Element;

use \ephFrame\HTML\Tag;

class Element
{
	public $decorators = array();
	
	public $validators = array();
	
	public $filters = array();
	
	public $errors = array();
	
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
			if (is_array($this->{$k}) && is_array($v)) {
				$this->{$k} += $v;
			} else {
				$this->{$k} = $v;
			}
		}
		if ($this->decorators !== false && empty($this->decorators)) {
			$this->decorators = $this->defaultDecorators();
		}
		if ($this->validators !== false && empty($this->validators)) {
			$this->validators = $this->defaultValidators();
		}
		if ($this->filters !== false && empty($this->filters)) {
			$this->filters = $this->defaultFilters();
		}
	}
	
	protected function defaultDecorators()
	{
		return array(
			'label' => new \ephFrame\HTML\Form\Decorator\Label($this),
			'error' => new \ephFrame\HTML\Form\Decorator\Error($this),
			'description' => new \ephFrame\HTML\Form\Decorator\Description($this),
			'wrap' => new \ephFrame\HTML\Form\Decorator\HTMLTag($this),
		);
	}
	
	protected function defaultValidators()
	{
		
	}
	
	protected function defaultFilters()
	{
		return array(
			'Trim' => new \ephFrame\Filter\Trim(),
			'StripWhiteSpace' => new \ephFrame\Filter\StripWhiteSpace(),
			'StripNewlinews' => new \ephFrame\Filter\StripNewlines(),
			'StripTags' => new \ephFrame\Filter\StripTags(),
		);
	}
	
	public function hasError()
	{
		return count($this->errors) > 0;
	}
	
	public function validate()
	{
		if (is_array($this->validators)) foreach($this->validators as $validator) {
			if (!$validator->validate($this->data)) {
				$this->errors[] = $validator->message();
				return false;
			}
		}
		return true;
	}
	
	public function submit($data)
	{
		$this->data = $data;
		foreach($this->filters as $filter) {
			$this->data = $filter->apply($this->data);
		}
		return $this;
	}
	
	public function tag()
	{
		$this->attributes['value'] = $this->data;
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