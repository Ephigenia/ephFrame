<?php

abstract class FormField extends HTMLTag {
	
	/**
	 * 	@var Form
	 */
	public $form;
	
	/**
	 * 	@var string
	 */
	public $type = 'text';
	
	/**
	 * 	@var HTMLTag
	 */
	public $label;
	
	/**
	 * 	@var boolean
	 */
	public $mandatory = false;
	
	/**
	 * 	Validation rule for this form field, checked in {@link Validate}
	 * 	@var array
	 */
	public $validate = array();

	public function __construct($name, $value = null, Array $attributes = array()) {
		$attributes['type'] = $this->type;
		if ($value !== null) {
			$attributes['value'] = $value;
		}
		$attributes['name'] = $name;
		if (empty($this->label) && $this->label !== false) {
			$this->label = ucwords(preg_replace('/_+/', ' ', $name)).':';
		}
		$this->label = new HTMLTag('label', array('for' => $name), $this->label);
		return parent::__construct('input', $attributes);
	}
	
	public function mandatory($value) {
		$this->mandatory = $value;
		return $this;
	}
	
	public function label($label) {
		$this->label->tagValue = $label;
		return $this;
	}
	
	public function value() {
		if (isset($this->form) && $this->form->submitted()) {
			return $this->form->request->data[$this->attributes->name];
		}
		return false;
	}
	
	public function validate($value = null) {
		if (func_num_args() == 0) {
			$value = $this->value();
		}
		if ($this->mandatory && empty($value)) {
			return false;
		}
		$validator = new Validator($this->validate, $this);
		return $validator->validate($value);
	}
	
	public function beforeRender() {
		// get posted value and set it as value for this field
		if ($value = $this->value()) {
			if ($this->type == 'textarea') {
				$this->tagValue = $value;
			} else {
				$this->attributes->value = $value;
			}
		}
		return parent::beforeRender();
	}
	
	public function render() {
		if (!$this->beforeRender()) return false;
		$rendered = '';
		if (!empty($this->label->tagValue)) {
			$rendered .= $this->label->render();
		}
		$rendered .= parent::render();
		if ($this->type !== 'hidden') {
			$rendered = '<p>'.$rendered.'</p>'.LF;
		}
		return ($this->afterRender($rendered));
	}
	
}

?>