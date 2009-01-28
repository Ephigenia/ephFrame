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
	public $errors = array();
	
	/**
	 *	Name of Models from the controller that should autamticly used in this
	 * 	form and more configuration stuff.
	 * 	Set this to false if you don’t want the form to be auto generate
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
//		foreach($this->__parentClasses($this) as $class) {
//			$classVars = get_class_vars($class);
//			if (isset($classVars['configureModel'])) {
//				foreach($classVars['configu'])
//			}
//		}
		//$this->configureModel = $this->__mergeParentProperty('configureModel');
		if ($action != null) {
			$this->action = $action;
		}
		$this->fieldset = new HTMLTag('fieldset');
		$this->addChild($this->fieldset);
		$attributes = array_merge(array('action' => &$this->action, 'method' => 'post', 'accept-charset' => 'UTF-8', 'id' => get_class($this)), $attributes);
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
	 *	Callback that is called right before controller calls his action
	 * 	@return true
	 */
	public function beforeAction() {
		return true;
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
			// add mulitpart if there are any files in the form
			foreach($this->fieldset->children() as $child) {
				if ($child instanceof FormFieldFile) {
					$this->attributes->set('enctype', 'multipart/form-data');
					break;
				}
			}
		}
		// add error messages on the top of the form
		if ($this->submitted() && (!$this->validate() || count($this->errors) > 0)) {
			$this->prepend(new HTMLTag('p', array('class' => 'error'), nl2br(implode(LF, $this->errors))));
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
	 *	Delete the field named $fieldname
	 * 
	 * 	@param $fieldname
	 * 	@return boolean
	 */
	public function delete($fieldname = null) {
		foreach($this->fieldset->children() as $field) {
			if ($field->attributes->name == $fieldname) {
				$field->delete();
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 	Add one or more new {@link FormField} to this form
	 * 
	 * 	You can also add multiple form elements by passing arguments:
	 * 	<code>
	 * 	$form->add($emailField, $passwordField);
	 * 	</code>
	 * 
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
	
	/**
	 *	Alias for {@link add}
	 *	@param FormField $field
	 *	@return Form
	 */
	public function addField(FormField $field) {
		return $this->add($field);
	}
	
	/**
	 *	Create a new Form Field and return it
	 * 	@param string $type
	 * 	@param string $name
	 * 	@param mixed $value
	 * 	@param array(string) $attributes
	 * 	@return FormField
	 */
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
	 *	Checks if the form is submitted.
	 * 
	 * 	This will check if the form has been submitted by iterating over all
	 * 	elements and check if there is any data submitted.
	 * 	@return boolean
	 */
	public function submitted() {
		// test if a form was submitted by checking every field of the form
		// for content
		foreach($this->fieldset->children as $child) {
			if ($child instanceof FormFieldFile) {
				if (empty($_FILES[$child->attributes->name])) continue;
			} else if (empty($this->request->data[$child->attributes->name])) {
				continue;
			}
			return true;
		}
		return false;
	}
	
	/**
	 *	This is a shortcut method combining {@link submitted} and {@link validate}
	 * 	so it returns true if the form is submitted and no errors and false if
	 * 	not submitted or errors
	 * 	@return boolean
	 */
	public function ok() {
		return ($this->submitted() && $this->validate());
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
			$this->errors = $validationErrors;
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
		if (empty($this->configureModel) && $this->configureModel !== false) {
			$possibleModelName = substr(get_class($this), 0, -4);
			if (isset($this->controller->$possibleModelName)) {
				$this->configureModel[] = $possibleModelName;
			}
		}
		if (!empty($this->configureModel)) {
			// catch $this->configureModel = 'User';
			if (!is_array($this->configureModel)) {
				$this->configureModel = array($this->configureModel);
			}
			// parse every model definition array for the form
			foreach($this->configureModel as $modelName => $config) {
				// create valid config array if not array or not properly filled
				if (!is_array($config)) {
					$modelName = $config;
					$config = array();
				}
				if (!isset($config['ignore'])) $config['ignore'] = array();
				if (!isset($config['fields'])) $config['fields'] = array();
				// add fields depending on model if model attached to controller	
				if (isset($this->controller->{$modelName})) {
					$this->configureModel($this->controller->$modelName, $config['ignore'], $config['fields']);
				} else {
					Log::write(Log::VERBOSE, get_class($this).': missing model to configure in controller: '.$modelName);
				}
			}
		}
		// add missing submit field if missing
		if (!$this->childWithAttribute('type', 'submit')) {
			$this->add($this->newField('submit', 'submit', 'submit'));
		}
		return true;
	}
	
	/**
	 *	FieldName type depending on field names mapping
	 * 	@var array(string)
	 */
	public $fieldNameFormTypeMapping = array(
		'email' 	=> 'email',
		'url' 		=> 'url',
		'text'		=> 'textarea',
		'password' 	=> 'password'
	);
	
	/**
	 *	Create form fields based on model structure
	 * 	
	 * 	@param Model $model
	 * 	@param array(string) $ignore
	 * 	@return Form
	 */
	public function configureModel(Model $model, Array $ignore = array(), Array $fields = array()) {
		if (empty($ignore)) {
			$ignore = array('id');
		}
		// just display these fields (ordered)
		$fieldInfos = array();
 		if (count($fields) > 0) {
 			foreach($fields as $fieldName) {
 				if (!isset($model->structure[$fieldName])) continue;
 				$fieldInfos[$fieldName] = $model->structure[$fieldName];
 			}
 		} else {
 			$fieldInfos = $model->structure;
 		}
 		// remove ignored fields
 		if (count($ignore) > 0) {
 			foreach($ignore as $ignoredFieldName) unset($fieldInfos[$ignoredFieldName]);
 		}
 		// parse field infos and create form fields for them
		foreach($fieldInfos as $fieldInfo) {
			$field = false;
			// create form field depending on db-table field type
			if (in_array($fieldInfo->name, $this->fieldNameFormTypeMapping)) {
				$field = $this->newField($this->fieldNameFormTypeMapping[$fieldInfo->name], $fieldInfo->name);
			} else {
				switch($fieldInfo->type) {
					case 'varchar':
					case 'int':
					case 'float':
						$field = $this->newField('text', $fieldInfo->name);
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
					case 'char':
						$field = $this->newField('checkbox', $fieldInfo->name, true);
						break;
					case 'enum':
						// enum can be checkbox
						if (count($fieldInfo->enumOptions) == 2) {
							$field = $this->newField('checkbox', $fieldInfo->name, true); 
						}
						break;
				}
			}
			if ($field) {
				// copy validation rules from model to form field if possible
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
				// checkboxes need special treatment
				if ($field->type == 'checkbox' && (in_array($fieldInfo->type, array('char', 'enum')))) {
					$field->value(true);
					if ($model->get($fieldInfo->name)) {
						$field->checked(true);
					} else {
						$field->checked(false);
					}
				} elseif (in_array($fieldInfo->name, array('created', 'edited'))) {
					$field->value(date('d.m.Y H:i', $model->get($fieldInfo->name)));
				} else {
					$field->value($model->get($fieldInfo->name));
				}
			}
		}
		return $this;
	}
	
	/**
	 * 	Returns all names of all form fields in the form
	 * 	@return array(string)
	 */
	public function fieldNames() {
		$fieldNames = array();
		foreach($this->fieldset->children() as $formField) {
			if (!$formField instanceof FormField) continue;
			$fieldNames[] = $formField->attributes->name;
		}
		return $fieldNames;
	}
	
}

/**
 * 	@package ephFrame
 *	@subpackage ephFrame.exceptions
 */
class FormException extends ObjectException {}

?>