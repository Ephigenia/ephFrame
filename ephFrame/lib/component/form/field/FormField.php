<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

/**
 * Abstract Form Field Class
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.component.form.Field
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 04.11.2008
 */
abstract class FormField extends HTMLTag
{	
	/**
	 * Stores an instance to the form this Form Field belongs to
	 * @var Form
	 */
	public $form;
	
	/**
	 * @var string
	 */
	public $type = 'text';
	
	/**
	 * @var HTMLTag
	 */
	public $label;
	
	/**
	 * @var boolean
	 */
	public $mandatory = true;
	
	/**
	 * Stores a error message if validation failed for this form field, if no
	 * specific error message was set and an error occured this is true.
	 * @var string|boolean
	 */
	public $error = false;
	
	/**
	 * Stores a description for the form field
	 * @var string|boolean
	 */
	public $description = false;
	
	/**
	 * Validation rule for this form field, checked in {@link Validate}
	 * Same format as in model.
	 * @var array
	 */
	public $validate = array();
	
	/**
	 * Standard sgml tag name for input fields is input
	 * @var string
	 */
	public $tagName = 'input';

	/**
	 * Creates a new Formfield with the $name, $value and $attributes
	 * @param string $name
	 * @param string $value
	 * @param array(string) array of attributes
	 */
	public function __construct($name, $value = null, Array $attributes = array()) 
	{
		$attributes['type'] = $this->type;
		$attributes['name'] = &$name;
		if (empty($this->label) && $this->label !== false) {
			$this->label = ucwords(preg_replace('/_+/', ' ', $name));
		}
		parent::__construct($this->tagName, $attributes);
		$this->value($value);
		$this->afterConstruct();
		return $this;
	}
	
	/**
	 * Callback for custom logic
	 * @return boolean
	 */
	public function afterConstruct() 
	{
		return true;
	}
	
	/**
	 * Set form field to be mandatory or not
	 * @param boolean $value
	 * @return FormField
	 */
	public function mandatory($value) 
	{
		$this->mandatory = (bool) $value;
		return $this;
	}
	
	/**
	 * Set description for form field
	 * @param string $description
	 * @return FormField
	 */
	public function description($description) 
	{
		$this->description = $description;
		return $this;
	}
	
	public function label($label) 
	{
		$this->label = $label;
		return $this;
	}
	
	/**
	 * Set or return form field value
	 * 
	 * @param string $value
	 * @return string|FormField
	 */
	public function value($value = null) 
	{
		// set form field value
		if (func_num_args() == 1) {
			if ($value !== null) {
				$this->attributes->set('value', $value);
			}
			return $this;
		}
		// return value, form submitted
		if (isset($this->form) && $this->form->submitted()) {
			$fieldname = $this->attributes->name;
			if (substr($fieldname, -2) == '[]') {
				$fieldname = substr($fieldname, 0, -2);
			}
			if (isset($this->form->request->data[$fieldname])) {
				return $this->form->request->data[$fieldname];
			}
		}
		return false;
	}
	
	/**
	 * Checks if this field has any submitted value
	 * @return boolean
	 */
	public function isEmpty() 
	{
		$val = $this->value();
		return empty($val);
	}
	
	/**
	 * Adds an validation Rule to the validation error, allready existent
	 * rules will be overwritten.
	 * @param array(string)
	 * @return FormField
	 */
	public function addValidationRule(Array $validationRule) 
	{
		if (ArrayHelper::dimensions($validationRule) == 1) {
			$this->validate[] = $validationRule;
		} else {
			$this->validate = array_merge($this->validate, $validationRule);
		}
		return $this;
	}
	
	/**
	 * Validate a single value or the current form field value and returns
	 * the result. If the validation fails the error message is stored in 
	 * $error:
	 * <code>
	 * if ($formField->validate('testvalue')) {
	 * 	echo $FormField->error;
	 * }
	 * </code>
	 * 
	 * @param string|mixed $value
	 * @return string|boolean
	 */
	public function validate($value = null)
	{
		if (func_num_args() == 0) {
			$value = $this->value();
		}
		if (empty($value)) {
			if ($this->mandatory) {
				if (function_exists('__')) {
					$this->error = __('Formfield <q>:1</q> is required.', coalesce($this->attributes->label, $this->attributes->name));
				} else {
					$this->error = coalesce($this->attributes->label, $this->attributes->name).' is required.';
				}
			}
		} else {
			$validator = new Validator($this->validate, $this);
			if (($result = $validator->validate($value)) !== true) {
				$this->error = $result;
				if ($this->error === false) {
					$this->error = $this->attributes->name.' invalid';
				}
			}
		}
		return (empty($this->error) && $this->error !== true);
	}
	
	public function beforeRender()
	{
		if (!$this->attributes->get('class') || !strpos($this->attributes->get('class'), $this->attributes->name)) {
			$this->attributes->appendTo('class', ' '.$this->attributes->name);
		}
		if ($this->mandatory && (!$this->attributes->get('class') || !strpos($this->attributes->get('class'), 'required'))) {
			$this->attributes->appendTo('class', ' required');
		}
		if ((!$this->validate() || isset($this->form->errors[$this->attributes->name]))
			&& $this->form->submitted() && (!$this->attributes->get('class') || !strpos($this->attributes->get('class'), 'error'))) {
			$this->attributes->appendTo('class', ' error');
		}
		if ($value = $this->value()) {
			$this->value($value);
		}
		$this->tagValue = strtr($this->tagValue, array(
			'&' => '&amp;',
			'>' => '&gt;',
			'<' => '&lt;',
		));
		return parent::beforeRender();
	}
	
	public function render($escaped = false) 
	{
		$rendered = '';
		if (!empty($this->label) && $this->type != 'hidden') {
			$labelTag = new HTMLTag('label', array('for' => $this->attributes->name), $this->label);
			$rendered .= $labelTag->render();
		}
		if ($this->type == 'checkbox') {
			$rendered = parent::render().$rendered;
		} else {
			$rendered .= parent::render();
		}
		// @todo encapsulate <p> decorator into decorator class or element
		if (strtolower($this->type) !== 'hidden') {
			$rendered = '<p class="'.lcFirst(substr(get_class($this), 9)).' '.$this->attributes->name.'">'.$rendered.'</p>'.LF;
		}
		return ($this->afterRender($rendered));
	}
	
	public function insertAfter(Tree $field) 
	{
		$field->form = $this->form;
		return parent::insertAfter($field);
	}
	
	public function insertBefore(Tree $field) 
	{
		$field->form = $this->form;
		return parent::insertBefore($field);
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 * @author Ephigenia // Marcel Eichner <love@ephigenia.de>
 * @since 26.05.2009
 */
class FormFieldException extends BasicException 
{} 