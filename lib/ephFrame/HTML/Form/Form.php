<?php

namespace ephFrame\HTML\Form;

class Form extends \ArrayObject
{
	public $fieldsets = array();
	
	public $attributes = array(
		'method' => 'post',
		'accept-charset' => 'utf-8',
	);
	
	public $decorators = array();
	
	public $errors = array();
	
	public $description;
	
	public function __construct(Array $attributes = array(), Array $data = array())
	{
		$this->fieldsets[] = new Fieldset();
		$this->attributes += $attributes;
		$this->decorators += $this->defaultDecorators();
		$this->configure();
		$this->bind($data);
		return $this;
	}
	
	public function error()
	{
		if (count($this->errors) > 0) {
			return $this->errors;
		} else {
			return false;
		}
	}
	
	public function defaultDecorators()
	{
		return array(
			'error' => new \ephFrame\HTML\Form\Decorator\Error($this, array('position' => \ephFrame\HTML\Form\Decorator\Position::INSERT_BEFORE)),
			'description' => new \ephFrame\HTML\Form\Decorator\Description($this, array('position' => \ephFrame\HTML\Form\Decorator\Position::INSERT_BEFORE)),
		);
	}
	
	public function configure() { }
	
	public function offsetSet($key, $element)
	{
		if (!($element instanceof \ephFrame\HTML\Form\Element\Element)) {
			throw new \InvalidArgumentException();
		}
		foreach($this->fieldsets as $i => $fieldset) foreach($fieldset as $j => $element) {
			if ($element->attributes['name'] != $key) {
				continue;
			}
			$this->fieldsets[$i][$j] = $element;
		}
		$this->fieldsets[0][$key] = $element;
		return $this;
	}
	
	public function offsetGet($key)
	{
		foreach($this->fieldsets as $fieldset) {
			if (isset($fieldset[$key])) {
				return $fieldset[$key];
			}
			foreach($fieldset as $element) {
				if ($element->attributes['name'] == $key) {
					return $element;
				}
			}
		}
		throw new \InvalidArgumentException(sprintf('Form field "%s" does not exist.', $key));
	}
	
	public function __get($key)
	{
		return $this->offsetGet($key);
	}
	
	public function offsetUnset($key)
	{
		foreach($this->fieldsets as $fieldset) {
			if (isset($fieldset[$key])) {
				unset($fieldset[$key]);
				return $this;
			}
			foreach($fieldset as $index => $element) {
				if ($element->attributes['name'] == $key) {
					unset($fieldset[$index]);
					return $this;
				}
			}
		}
		throw new \InvalidArgumentException(sprintf('Form field "%s" does not exist.', $key));
	}
	
	public function bind(Array $data = array())
	{
		foreach($this->fieldsets as $fieldset) foreach($fieldset as $element) {
			if (isset($data[$element->attributes['name']])) {
				$element->submit($data[$element->attributes['name']]);
			}
		}
		return $this;
	}
	
	public function ok()
	{
		foreach($this->fieldsets as $fieldset) foreach($fieldset as $element) {
			if (!empty($element->errors)) return false;
		}
		return true;
	}
	
	public function isValid()
	{
		foreach($this->fieldsets as $fieldset) foreach($fieldset as $element) {
			if (!$element->validate($element->data)) return false;
		}
		return true;
	}
	
	public function tag()
	{
		return new \ephFrame\HTML\Tag('form', implode(PHP_EOL, $this->fieldsets), $this->attributes + array('escaped' => false));
	}
	
	public function toArray()
	{
		$array = array();
		foreach($this->fieldsets as $fieldset) foreach($fieldset as $element) {
			$array[$element->attributes['name']] = $element->data;
		}
		return $array;
	}
	
	public function __toString()
	{
		$rendered = $this->tag();
		if (is_array($this->decorators)) foreach($this->decorators as $decorator) {
			$decorator->element = $this;
			$rendered = $decorator->decorate($rendered);
		}
		return (string) $rendered;
	}
}
