<?php

ephFrame::loadClass('ephFrame.lib.HTMLTag');

/**
 * 	@todo add validation rules here or make validation rules easily editable by sub classes
 * 	@version 0.3
 */
class Form extends HTMLTag {
	
	/**
	 *	Instance of Controller if Form is used as component
	 * 	@var Controller
	 */
	protected $controller;
	
	protected $action = './';
	
	/**
	 * 	@var HTTPRequest
	 */
	protected $request;
	
	/**
	 * 	@var HTMLTag
	 */
	protected $fieldset;
	
	/**
	 * 	Creates a new Form Instance
	 * 	
	 * 	@param string $action
	 * 	@return Form
	 */
	public function __construct($action = null, Array $attributes = array()) {
		$this->request = new HTTPRequest(true);
		if ($action == null) {
			$action = $this->action;
		}
		$this->fieldset = new HTMLTag('fieldset');
		$this->addChild($this->fieldset);
		$attributes = array_merge(array('action' => &$action, 'method' => 'post', 'accept-charset' => 'UTF-8'), $attributes);
		return parent::__construct('form', $attributes);
	}
	
	/**
	 *	Manual inherit Component startup method to use in sub classes, see docu
	 * 	in {@link Component}.
	 * 	@return boolean
	 */
	public function startup() {
		// set form variable for view
		if (isset($this->controller)) {
			$this->controller->set(get_class($this), $this);
			// set default action of form to the current url
			$this->attributes->action = WEBROOT.$this->controller->request->get('__url'); 
		}
	}
	
	/**
	 *	Manual inherit Component init method, see docu in {@link Component}.
	 * 	@return boolean
	 */
	public function init(Controller $controller) {
		$this->controller = $controller;
		return true;
	}
	
	/**
	 * 	Define your form in this method
	 */
	public function configure() {
		// in the classes that inherit this one, you create all the form fields
	}
	
	public function __get($fieldname) {
		if ($field = $this->fieldset->childWithAttribute('name', $fieldname)) {
			return $field;
		}
		return $this->{$fieldname};
	}
	
	/**
	 * 	@param FormField $field
	 * 	@return Form
	 */
	public function add(FormField $field) {
		if (func_num_args() > 1) {
			foreach(func_get_args() as $field) $this->add($field);
		} else {
			$field->form = $this;
			$this->fieldset->addChild($field);
		}
		return $this;
	}
	
	public function newField($type, $name, $value = null, Array $attributes = array()) {
		if (strpos($type, '.') == false) {
			$fieldClassname = 'FormField'.ucFirst($type);
			$fieldClassPath = 'ephFrame.lib.component.Form.Field.'.$fieldClassname;
		} else {
			$fieldClassname = ClassPath::className($type);
			$fieldClassPath = $type;
		}
		if (!class_exists($fieldClassname)) {
			ephFrame::loadClass($fieldClassPath);
		}
		return new $fieldClassname($name, $value, $attributes);
	}
	
	public function submitted() {
		// test if a form was submitted by checking every field of the form
		// for content
		foreach($this->fieldset->children as $child) {
			if (!isset($this->request->data[$child->attributes->name])) continue;
			return true;
		}
		return false;
	}
	
	public function validate(Array $fieldNames = array()) {
		$validationErrors = array();
		foreach($this->children() as $child) {
			// skip form fields with the names from $fieldNames
			if (!empty($fieldNames) && !in_array($child->attributes->name, $fieldNames)) continue;
			// validate form field and save errors in return array
			$r = $child->validate();
			if ($r !== true) {
				$validationErrors[$child->attributes->name] = $r;
			}
		}
		// finally return the resulting errors
		if (empty($validationErrors)) {
			return true;
		} else {
			return $validationErrors;
		}
	}
	
}

/**
 * 	@package ephFrame
 *	@subpackage ephFrame.exceptions
 */
class FormException extends ObjectException {}

?>