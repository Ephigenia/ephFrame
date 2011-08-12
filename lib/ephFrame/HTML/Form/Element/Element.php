<?php

namespace ephFrame\HTML\Form\Element;

use 
	\ephFrame\HTML\Tag,
	\ephFrame\core\Configurable
	;

class Element extends Configurable
{
	public $decorators = array();
	
	public $validators = array();
	
	public $required = true;
	
	public $filters = array();
	
	public $errors = array();
	
	protected $tag = 'input';
	
	public $description;
	
	public $label;
	
	public $data;
	
	public $attributes = array();
	
	public function __construct($name = null, $value = null, Array $options = array())
	{
		if ($name === null) {
			$this->name = \ephFrame\util\String::lower(get_class($this));
		}
		$this->attributes += array(
			'name' => $name,
			'value' => $value
		);
		if ($this->decorators !== false && empty($this->decorators)) {
			$this->decorators = $this->defaultDecorators();
		}
		if ($this->validators !== false && empty($this->validators)) {
			$this->validators = $this->defaultValidators();
		}
		if ($this->filters !== false && empty($this->filters)) {
			$this->filters = $this->defaultFilters();
		}
		return parent::__construct($options);
	}
	
	protected function defaultDecorators()
	{
		return array(
			'label' => new \ephFrame\HTML\Form\Decorator\Label($this),
			'description' => new \ephFrame\HTML\Form\Decorator\Description($this),
			'error' => new \ephFrame\HTML\Form\Decorator\Error($this),
			'wrap' => new \ephFrame\HTML\Form\Decorator\HTMLTag($this),
		);
	}
	
	protected function defaultValidators()
	{
		return array(
			'notEmpty' => new \ephFrame\validator\NotEmpty(),
		);
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
	
	public function error()
	{
		if (count($this->errors) > 0) {
			return $this->errors;
		} else {
			return false;
		}
	}
	
	public function ok()
	{
		return !((bool) $this->error());
	}
	
	public function validate($value)
	{
		if (is_array($this->validators)) foreach($this->validators as $validator) {
			if ($validator->validate($value)) continue;
			$this->errors[] = $validator->message();
			return false;
		}
		return true;
	}
	
	public function submit($data)
	{
		$this->data = $data;
		if (is_array($this->filters)) foreach($this->filters as $filter) {
			$this->data = $filter->apply($this->data);
		}
		if ($this->required) {
			$this->validate($this->data);
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
		$rendered = '';
		if (is_array($this->decorators)) {
			foreach($this->decorators as $decorator) {
				$decorator->element = $this;
			}
			$rendered = $this->tag();
			foreach($this->decorators as $decorator) {
				$rendered = $decorator->decorate($rendered);
			}
		} else {
			$rendered = $this->tag();
		}
		return (string) $rendered;
	}
}