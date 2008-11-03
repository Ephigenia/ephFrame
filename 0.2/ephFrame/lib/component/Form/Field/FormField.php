<?php

// @todo add method, var to add validation rules (from a model)
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
	 *	Stores a error message if validation failed for this form field
	 * 	@var string|boolean
	 */
	public $error = false;
	
	/**
	 * 	Validation rule for this form field, checked in {@link Validate}
	 * 	@var array
	 */
	public $validate = array();

	public function __construct($name, $value = null, Array $attributes = array()) {
		$attributes['type'] = $this->type;
		$attributes['name'] = $name;
		if (empty($this->label) && $this->label !== false) {
			$this->label = ucwords(preg_replace('/_+/', ' ', $name)).':';
		}
		$this->label = new HTMLTag('label', array('for' => $name), $this->label);
		parent::__construct('input', $attributes);
		$this->value($value);
		return $this;
	}
	
	public function mandatory($value) {
		$this->mandatory = $value;
		return $this;
	}
	
	public function label($label) {
		$this->label->tagValue = $label;
		return $this;
	}
	
	public function value($value = null) {
		if (func_num_args() == 1) {
			if ($value !== null) {
				$this->attributes->set('value', $value);
			}
		} else {
			if (isset($this->form) && $this->form->submitted() && isset($this->form->request->data[$this->attributes->name])) {
				return $this->form->request->data[$this->attributes->name];
			}
			return false;
		}
	}
	
	/**
	 *	Validates the content of the form field that was submitted or the passed
	 * 	$value. If no $value is submitted and the result is not true the form
	 * 	field is marked as errous by setting {@link $error}.
	 *  This method is used by the Form Class validate method.
	 * 	@param string|mixed $value
	 * 	@return string|boolean
	 */
	public function validate($value = null) {
		if (func_num_args() == 0) {
			$value = $this->value();
		}
		if ($this->mandatory && empty($value)) {
			return false;
		}
		$validator = new Validator($this->validate, $this);
		$result = $validator->validate($value);
		if ($result !== true && func_num_args() == 0) {
			$this->error = $result;
		}
		return $result;
	}
	
	public function beforeRender() {
		// add style class to form element
		// @todo somehow this happens to be called twice?
		if ($this->attributes->isEmpty('class')) {
			$this->attributes->set('class', $this->attributes->name);
		} elseif ($this->attributes->get('class') != $this->attributes->name) {
			$this->attributes['class'] .= ' '.$this->attributes->name;
		}
		// does not work because this method is called two times?, see the comment above
		if (!$this->validate()) {
			$this->attributes['class'] .= ' errousField';
		}
		
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
	
	public function insertAfter(Tree $field) {
		$field->form = $this->form;
		return parent::insertAfter($field);
	}
	
	public function insertBefore(Tree $field) {
		$field->form = $this->form;
		return parent::insertBefore($field);
	}
	
}

?>