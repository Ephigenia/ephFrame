<?php

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

if (!class_exists('HTMLTag')) ephFrame::loadClass('ephFrame.lib.HTMLTag');

/**
 * 	Form Class
 * 	
 * 	@todo add validation rules here or make validation rules easily editable by sub classes
 * 	@version 0.3
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component.Form
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 04.11.2008
 */
class Form extends HTMLTag {
	
	/**
	 *	Instance of Controller if Form is used as component
	 * 	@var Controller
	 */
	protected $controller;
	
	/**
	 * 	Stores the submit action target
	 * 	@var string
	 */
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
	 *	Stores tha errors from a validation process
	 * 	@var array(string)
	 */
	public $validationErrors = array();
	
	/**
	 *	Name of Models from the controller that should autamticly used in this
	 * 	form
	 * 	@param array(string)
	 */
	public $configureModel = array();
	
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
	
	public function beforeRender() {
		// add multipart form data if file field in the form
		if (!($this->attributes->hasKey('enctype'))) {
			foreach($this->fieldset->children() as $child) {
				if ($child instanceof FormFieldFile) {
					$this->attributes->set('enctype', 'multipart/form-data');
					break;
				}
			}
		}
		if ($this->submitted() && (!$this->validate() || count($this->validationErrors) > 0)) {
			$this->prepend(new HTMLTag('p', array('class' => 'error'), nl2br(implode(LF, $this->validationErrors))));
		}
		return parent::beforeRender();
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
	
	/**
	 *	Checks if the form is submitted by iterating over all elements and return
	 * 	true if any of these fields was submitted
	 * 	@return boolean
	 */
	public function submitted() {
		// test if a form was submitted by checking every field of the form
		// for content
		foreach($this->fieldset->children as $child) {
			if (empty($this->request->data[$child->attributes->name])) continue;
			return true;
		}
		return false;
	}
	
	public function validate(Array $fieldNames = array()) {
		$validationErrors = false;
		foreach($this->fieldset->children() as $child) {
			// skip form fields with the names from $fieldNames
			if (!empty($fieldNames) && !in_array($child->attributes->name, $fieldNames)) continue;
			// skip non-form-fields
			if (!($child instanceof FormField)) continue;
			// validate form field and save errors in return array
			if ($child->validate() !== true) {
				$validationErrors[$child->attributes->name] = $child->error;
			}
		}
		// finally return the resulting errors
		if (!$validationErrors) {
			return true;
		} else {
			$this->validationErrors = $validationErrors;
			return false;
		}
	}
		
	/**
	 * 	Define additional form elements in you application forms.
	 * 	You can automaticly fill you application models using model structure:
	 * 
	 * 	If youre form is named CommentForm and you have a model Comment then
	 * 	the form will try to get the form field structure from the Model
	 * 	structure.
	 * 
	 * 	You also cann exclude structure field names when importing model
	 * 	structure:
	 * 	<code>
	 * 	public $configureModel = array('User' => array('id', 'email'));
	 * 	</code>
	 * 	
	 * 	But you also can use this callback to  create your own forms:
	 * 	<code>
	 * 	// be sure to call parent::configure();
	 * 	$this->add($this->newField('text', 'username'));
	 * 	</code>
	 * 	
	 * 	@return true
	 */
	public function configure() {
		if (empty($this->configureModel)) {
			$possibleModelName = substr(get_class($this), 0, -4);
			if (isset($this->controller->$possibleModelName)) {
				$this->configureModel[] = $possibleModelName;
			}
		}
		if (!empty($this->configureModel)) {
			if (!is_array($this->configureModel)) {
				$this->configureModel = array($this->configureModel);
			}
			foreach($this->configureModel as $modelName => $config) {
				if (!is_array($config)) {
					$modelName = $config;
					$config = array('ignore' => array());
				}
				if (isset($this->controller->$modelName)) {
					$this->configureModel($this->controller->$modelName, $config['ignore']);
				}
			}
		}
		if (!$this->childWithAttribute('type', 'submit')) {
			$this->add($this->newField('submit', 'submit', 'submit'));
		}
		return true;
	}
	
	/**
	 *	Adds form fields by parsing model structure, ignoring the model fields
	 * 	with the $ignore names
	 * 		
	 * 	@return Form
	 */
	public function configureModel(Model $model, Array $ignore = array()) {
		foreach($model->structure as $fieldInfo) {
			if (count($ignore) > 0 && in_array($fieldInfo->name, $ignore)) continue;
			$field = false;
			switch($fieldInfo->type) {
				case 'varchar':
					if ($fieldInfo->name == 'password') {
						$field = $this->newField('password', $fieldInfo->name);
					} elseif ($fieldInfo->name == 'email') {
						$field = $this->newField('email', $fieldInfo->name);
					} else {
						$field = $this->newField('text', $fieldInfo->name);
					}
					break;
				case 'blob':
				case 'text':
				case 'mediumtext':
				case 'mediumblob':
				case 'tinyblob':
				case 'tinytext':
				case 'longblob':
				case 'longtext':
					$field = $this->newField('textarea', $fieldInfo->name);
					break;
				case 'date':
					$field = $this->newField('text', $fieldInfo->name, gmdate('Y-m-d'));
					break;	
			}
			if ($field) {
				// add validation rules from model to field
				if (isset($model->validate[$fieldInfo->name])) {
					$field->addValidationRule($model->validate[$fieldInfo->name]);
				}
				$this->add($field);
			}
		}
		return $this;
	} 
	
	/**
	 *	Fills the form fields with data from a model
	 * 	@return Form
	 */
	public function fillModel(Model $model) {
		if (!$this->submitted()) {
			// only fill with model data if form was not submitted
			foreach($model->structure as $fieldInfo) {
				if (!($field = $this->fieldset->childWithAttribute('name', $fieldInfo->name))) continue;
				$field->value($model->get($fieldInfo->name));
			}
		}
		return $this;
	}
	
	
}

/**
 * 	@package ephFrame
 *	@subpackage ephFrame.exceptions
 */
class FormException extends ObjectException {}

?>