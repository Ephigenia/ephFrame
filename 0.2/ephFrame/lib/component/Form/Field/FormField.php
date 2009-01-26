<?php

// @todo add method, var to add validation rules (from a model)
/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright 2007+, Ephigenia M. Eichner, Kopernikusstr. 8, 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 	@license http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright Copyright 2007+, Ephigenia M. Eichner
 * 	@link http://code.ephigenia.de/projects/ephFrame/
 * 	@filesource
 */

/**
 *	Abstract Form Field Class
 * 
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component.Form.Field
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 04.11.2008
 */
abstract class FormField extends HTMLTag {
	
	/**
	 * 	Stores an instance to the form this Form Field belongs to
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
	 *	Stores a error message if validation failed for this form field, if no
	 * 	specific error message was set and an error occured this is true.
	 * 	@var string|boolean
	 */
	public $error = false;
	
	/**
	 *	Stores a description for the form field
	 * 	@var string|boolean
	 */
	public $description = false;
	
	/**
	 * 	Validation rule for this form field, checked in {@link Validate}
	 * 	Same format as in model.
	 * 	@var array
	 */
	public $validate = array();
	
	/**
	 *	Standard sgml tag name for input fields is input
	 * 	@var string
	 */
	public $tagName = 'input';

	/**
	 *	Creates a new Formfield with the $name, $value and $attributes
	 * 	@param string $name
	 * 	@param string $value
	 * 	@param array(string) array of attributes
	 */
	public function __construct($name, $value = null, Array $attributes = array()) {
		$attributes['type'] = $this->type;
		$attributes['name'] = &$name;
		$attributes['id'] = &$name;
		if (empty($this->label) && $this->label !== false) {
			$this->label = ucwords(preg_replace('/_+/', ' ', $name)).':';
		}
		$this->label = new HTMLTag('label', array('for' => $name), $this->label);
		parent::__construct($this->tagName, $attributes);
		$this->value($value);
		$this->afterConstruct();
		return $this;
	}
	
	public function afterConstruct() {
		
	}
	
	public function mandatory($value) {
		$this->mandatory = $value;
		return $this;
	}
	
	public function description($description) {
		$this->description = $description;
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
			return $this;
		} elseif (isset($this->form) && $this->form->submitted() && isset($this->form->request->data[$this->attributes->name])) {
			return $this->form->request->data[$this->attributes->name];
		}
		return false;
	}
	
	/**
	 *	Adds an validation Rule to the validation error, allready existent
	 * 	rules will be overwritten.
	 * 	@param array(string)
	 * 	@return FormField
	 */
	public function addValidationRule(Array $validationRule) {
		if (ArrayHelper::dimensions($validationRule) == 1) {
			$this->validate[] = $validationRule;
		} else {
			$this->validate = array_merge($this->validate, $validationRule);
		}
		return $this;
	}
	
	/**
	 *	Validate a single value or the current form field value and returns
	 * 	the result. If the validation fails the error message is stored in 
	 * 	$error:
	 * 	<code>
	 * 	if ($formField->validate('testvalue')) {
	 * 		echo $FormField->error;
	 *	}
	 * 	</code>
	 * 	
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
		if ($result !== true) {
			$this->error = $result;
			return false;
		}
		return true;
	}
	
	public function beforeRender() {
		// add style class to form element
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
			switch($this->type) {
				case 'textarea':
					$this->tagValue = $value;
					break;
				case 'checkbox':
					$this->check();
					break;
				default:
					$this->attributes->value = $value;
					break;
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