<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

class_exists('HTMLTag') or require dirname(__FILE__).'/../../HTMLTag.php';

/**
 * Form Class
 * 
 * @todo add validation rules here or make validation rules easily editable by sub classes
 * @version 0.3
 * @package ephFrame
 * @subpackage ephFrame.lib.component.Form
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 04.11.2008
 */
class Form extends HTMLTag 
{
	/**
	 * Instance of Controller if Form is used as component
	 * @var Controller
	 */
	protected $controller;
	
	/**
	 * Stores the submit action target
	 * @var string
	 */
	protected $action = './';
	
	/**
	 * @var HTTPRequest
	 */
	protected $request;
	
	/**
	 * @var HTMLTag
	 */
	protected $fieldset;
	
	/**
	 * Stores tha errors from a validation process
	 * @var array(string)
	 */
	public $errors = array();
	
	/**
	 * Store success messages
	 * @var array(string)
	 */
	public $successMessages = array();
	
	/**
	 * Array that describes the form, can contain model names and form field
	 * configuration as arrays. check docu for more
	 * @param array(string)
	 */
	public $config = array();
	
	/**
	 * Creates a new Form Instance
	 * 
	 * @param string $action
	 * @return Form
	 */
	public function __construct($action = null, Array $attributes = array()) 
	{
		$this->request = new HTTPRequest(true);
		if ($action != null) {
			$this->action = $action;
		}
		$this->fieldset = new HTMLTag('fieldset');
		$this->addChild($this->fieldset);
		$attributes = array_merge(
			array(
				'action' => &$this->action,
				'method' => 'post',
				'accept-charset' => 'UTF-8',
				'id' => get_class($this)
			),
			$attributes
		);
		return parent::__construct('form', $attributes);
	}
	
	/**
	 * Manual inherit Component startup method to use in sub classes, see docu
	 * in {@link Component}.
	 * @return boolean
	 */
	public function startup() 
	{
		// set form variable for view
		if (isset($this->controller)) {
			$this->controller->data->set(get_class($this), $this);
			$this->attributes->action = Router::uri();
		}
		return $this;
	}
	
	/**
	 * Callback that is called right before controller calls his action
	 * @return true
	 */
	public function beforeAction() 
	{
		return true;
	}
	
	/**
	 * Manual inherit Component init method, see docu in {@link Component}.
	 * @return boolean
	 */
	public function init(Controller $controller) 
	{
		$this->controller = $controller;
		return $this;
	}
	
	public function beforeRender() 
	{
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
		// add error and success messages at the top of the form
		if ($this->submitted()) {
			// error messages
			if (!$this->validate() && !isset($this->errorsDone)) {
				$this->prepend(new HTMLTag('p', array('class' => 'error'), nl2br(implode(LF, $this->errors))));
				$this->errorsDone = true;
			// success messages
			} elseif (!empty($this->successMessages) && !isset($this->successDone)) {
				if (!is_array($this->successMessages)) $this->successMessages = array($this->successMessages);
				$this->successDone = true;
				$this->prepend(new HTMLTag('p', array('class' => 'success'), nl2br(implode(LF, $this->successMessages))));
			}
		}
		return parent::beforeRender();
	}
	
	public function __get($fieldname) 
	{
		if ($field = $this->fieldset->childWithAttribute('name', $fieldname)) {
			return $field;
		}
		return $this->{$fieldname};
	}
	
	/**
	 * Delete the field named $fieldname
	 * 
	 * @param $fieldname
	 * @return boolean
	 */
	public function delete($fieldname = null) 
	{
		if ($field = $this->fieldset->childWithAttribute('name', $fieldname)) {
			$field->delete();
			return true;
		}
		return false;
	}
	
	/**
	 * Add one or more new {@link FormField} to this form
	 * 
	 * You can also add multiple form elements by passing arguments:
	 * <code>
	 * $form->add($emailField, $passwordField);
	 * </code>
	 * 
	 * @param FormField $field
	 * @return Form
	 */
	public function add(FormField $field) 
	{
		if (func_num_args() > 1) {
			foreach(func_get_args() as $field) $this->add($field);
		} else {
			$field->form = $this;
			$this->fieldset->addChild($field);
		}
		return $this;
	}
	
	/**
	 * Alias for {@link add}
	 * @param FormField $field
	 * @return Form
	 */
	public function addField(FormField $field) 
	{
		return $this->add($field);
	}
	
	/**
	 * Checks if this form has a field with the name $name
	 * 
	 * <code>
	 * if ($userForm->hasField('username')) {
	 * 	$userForm->username->value('Your Username');
	 * }
	 * </code>
	 * 
	 * @param string $name name of userfield to search for
	 * @return boolean
	 */
	public function hasField($name) 
	{
		return $this->fieldset->childWithAttribute('name', $name);
	}
	
	/**
	 * Create a new Form Field and return it
	 * @param string $type
	 * @param string $name
	 * @param mixed $value
	 * @param array(string) $attributes
	 * @return FormField
	 */
	public function newField($type, $name, $value = null, Array $attributes = array()) 
	{
		// load form field class
		if (strpos($type, '.') == false) {
			$classname = 'FormField'.ucFirst($type);
			$fieldClassPath = 'ephFrame.lib.component.Form.Field.'.$classname;
		} else {
			$classname = ClassPath::className($type);
			$fieldClassPath = $type;
		}
		if (!class_exists($classname)) {
			ephFrame::loadClass($fieldClassPath);
		}
		// create form field instance
		if (!empty($attributes['value'])) {
			$value = $attributes['value'];
			unset($attributes['value']);
		}
		$field = new $classname($name, $value, $attributes);
		// set additional attributes
		if (!empty($attributes['options'])) {
			// $field->addOptions($attributes['options']);
		}
		if (isset($attributes['label'])) {
			$field->label($attributes['label']);
		}
		if (isset($attributes['mandatory'])) {
			$field->mandatory = $attributes['mandatory'];
		}
		if (!empty($attributes['validate'])) {
			$field->addValidationRule($attributes['validate']);
		}
		return $field;
	}
	
	/**
	 * Checks if the form is submitted.
	 * 
	 * This will be true if the form has more than one elements and any of it
	 * filled or the single element of a form is filled and submitted.
	 * @return boolean
	 */
	public function submitted() 
	{
		// test if a form was submitted by checking every field of the form
		// for content
		$formFieldCount = 0;
		foreach($this->fieldset->children as $child) {
			if ($child instanceof FormField) $formFieldCount++;	
		}
		$filledFields = 0;
		foreach($this->fieldset->children as $child) {
			$val = false;
			if ($child instanceof FormFieldFile && isset($_FILES[$child->attributes->name])) {
				$val = $_FILES[$child->attributes->name];
				if (!empty($val)) {
					return true;
				}
			} elseif (isset($this->request->data[$child->attributes->name])) {
				$val = $this->request->data[$child->attributes->name];
			}
			if ($child instanceof FormFieldSubmit && !empty($val)) {
				return true;
			}
			if (!empty($val)) {
				$filledFields++;
			}
			if (($formFieldCount > 1 && $filledFields > 0) || ($formFieldCount <= 2 && $filledFields == 1)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * This is a shortcut method combining {@link submitted} and {@link validate}
	 * so it returns true if the form is submitted and no errors and false if
	 * not submitted or errors
	 * @return boolean
	 */
	public function ok() 
	{
		return ($this->submitted() && $this->validate());
	}
	
	/**
	 * Validates all fields of this form or the fields you passed as array
	 * @param $fieldNames
	 * @return boolean
	 */
	public function validate(Array $fieldNames = array()) 
	{
		if (!is_array($this->errors)) {
			$this->errors = array($this->errors);
		}
		if ($this->fieldset->hasChildren()) foreach($this->fieldset->children() as $child) {
			// skip form fields with the names from $fieldNames
			if (!empty($fieldNames) && !in_array($child->attributes->name, $fieldNames)) continue;
			// skip non-form-fields
			if (!($child instanceof FormField)) continue;
			// validate form field and save errors in return array
			if (!$child->validate()) {
				$this->errors[$child->attributes->name] = $child->error;
			}
		}
		return empty($this->errors);
	}
		
	/**
	 * Define additional form elements in you application forms.
	 * You can automaticly fill you application models using model structure:
	 * 
	 * If youre form is named CommentForm and you have a model Comment then
	 * the form will try to get the form field structure from the Model
	 * structure.
	 * 
	 * You also cann exclude structure field names when importing model
	 * structure:
	 * <code>
	 * public $configureModel = array('User' => array('id', 'email'));
	 * </code>
	 * 
	 * But you also can use this callback to  create your own forms:
	 * <code>
	 * // be sure to call parent::configure();
	 * $this->add($this->newField('text', 'username'));
	 * </code>
	 * 
	 * @return true
	 */
	public function configure() 
	{
		// backwards compatibility when configure was named configureMOdel
		// @DEPRECIATED & @OLD
		if (isset($this->configureModel)) {
			$this->config = array_merge($this->config, $this->configureModel);
		}
		// add model for a form if missing
		if (empty($this->config) && $this->config !== false) {
			$possibleModelName = substr(get_class($this), 0, -4);
			if (isset($this->controller->{$possibleModelName}) && $this->controller->{$possibleModelName} instanceof Model) {
				$this->config[] = $possibleModelName;
			}
		}
		// add models 
		if (!empty($this->config)) {
			// catch $this->configureModel = 'User';
			if (!is_array($this->config)) {
				$this->config = array($this->config);
			}
			// parse every model definition array for the form
			foreach($this->config as $modelName => $config) {
				// $configure = array(array('type' => 'submit', label => 'save')) notation
				if (is_int($modelName) && is_array($config)) {
					// merge with default values
					$config = array_merge(
						array(
							'type' => 'text',
							'name' => $config['type'],
						),
						$config
					);
					$field = $this->newField($config['type'], @$config['name'], null, $config);
					$this->addField($field);
				// $configure = array([modelname] => config) notation
				} else {
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
				} // if
			} // foreach
		} // if
		$this->afterConfig();
		return true;
	}
	
	/**
	 *	Callback called after {@link config} was called
	 *	@return boolean
	 */
	public function afterConfig() 
	{
		// add missing submit field if missing
		if (!$this->fieldset->childWithAttribute('type', 'submit')) {
			$this->add($this->newField('submit', 'submit', 'submit'));
		}
		return true;
	}
	
	/**
	 * FieldName type depending on field names mapping
	 * @var array(string)
	 */
	public $fieldNameFormTypeMapping = array(
		'email' 	=> 'email',
		'url' 		=> 'url',
		'text'		=> 'textarea',
		'password' 	=> 'password',
		'created'	=> 'dateTime',
		'updated'	=> 'dateTime'
	);
	
	/**
	 * Create form fields based on model structure
	 * 
	 * @param Model $model
	 * @param array(string) $ignore
	 * @return Form
	 */
	public function configureModel(Model $model, Array $ignore = array(), Array $fields = array()) 
	{
		if (empty($ignore)) {
			$ignore = array('id');
		}
		
		// just display these fields (ordered)
		$fieldInfos = array();
 		if (count($fields) > 0) {
 			foreach($fields as $fieldName => $config) {
 				if (is_int($fieldName)) {
 					$fieldName = $config;
 					$config = array();
 				}
 				if (!isset($model->structure[$fieldName])) continue;
 				$config['modelFieldInfo'] = $model->structure[$fieldName];
 				$fieldInfos[$fieldName] = $config;
 			}
 		} else {
 			$fieldInfos = array();
 			foreach($model->structure as $g) {
 				$fieldInfos[$g->name]['modelFieldInfo'] = $g;
 			}
 		}
 		// remove ignored fields
 		if (count($ignore) > 0) {
 			foreach($ignore as $ignoredFieldName) unset($fieldInfos[$ignoredFieldName]);
 		}

 		// parse field infos and create form fields for them
		foreach($fieldInfos as $fieldInfo) {
			$modelFieldInfo = $fieldInfo['modelFieldInfo'];
			$fieldInfo['name'] = $modelFieldInfo->name;
			// type of field defined in field config
			if (!empty($fieldInfo['type'])) {
			// create form field depending on db-table field type
			} else if (array_key_exists($modelFieldInfo->name, $this->fieldNameFormTypeMapping)) {
				$fieldInfo['type'] = $this->fieldNameFormTypeMapping[$modelFieldInfo->name];
			} else {
				switch($modelFieldInfo->type) {
					case 'varchar': case 'int': case 'float':
						$fieldInfo['type'] = 'text';
						if ($modelFieldInfo->type == 'varchar') {
							$fieldInfo['maxLength'] = $modelFieldInfo->length;
						}
						break;
					case 'blob': case 'text': case 'mediumtext': case 'mediumblob':
					case 'tinyblob': case 'tinytext': case 'longblob': case 'longtext':
						$fieldInfo['type'] = 'textarea';
						break;
					case 'date':
						$fieldInfo['type'] = 'date';
						break;
					case 'char':
						$fieldInfo['type'] = 'checkbox';
						$fieldInfo['value'] = true;
						break;
					case 'enum':
						// enum can be checkbox
						if (count($modelFieldInfo->enumOptions) <= 2) {
							$fieldInfo['value'] = true;
							$fieldInfo['type'] = 'checkbox'; 
						} else {
							$fieldInfo['type'] = 'DropDown';
							$fieldInfo['value'] = $fieldInfo->enumOptions;
						}
						break;
				}
			}
			if (!empty($fieldInfo['type']) && !empty($fieldInfo['name'])) {
				// copy validation rules from model to form field if possible
				unset($fieldInfo['modelFieldInfo']);
				$field = $this->newField($fieldInfo['type'], $fieldInfo['name'], @$fieldInfo['value'], $fieldInfo);
				if (isset($model->validate[$fieldInfo['name']])) {
					$field->addValidationRule($model->validate[$fieldInfo['name']]);
				}
				if (isset($fieldInfo['options'])) {
					$field->addOptions($fieldInfo['options']);
				}
				if ($fieldInfo['type'] == 'enum' && count($modelFieldInfo->enumOptions) > 2) {
					foreach($modelFieldInfo->enumOptions as $optionValue) {
						$field->addOption($optionValue);
					}
				}
				$this->add($field);
			}
		}
		return $this;
	} 
	
	/**
	 * Fills the form fields with data from a model
	 * @return Form
	 */
	public function fillModel(Model $model) 
	{
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
				} else {
					$field->value($model->get($fieldInfo->name));
				}
			}
		}
		return $this;
	}
	
	/**
	 * Alias according to {@link toModel} for {@link fillModel}
	 * @param $model
	 * @return unknown_type
	 */
	public function fromModel(Model $model) 
	{
		return $this->fillModel($model);
	}
	
	/**
	 * Assign submitted values to a model
	 *
	 * <code>
	 * // in a controller action
	 * $this->UserForm->toModel($this->User);
	 * </code>
	 * 
	 * @param Model $model
	 * @param array(string) $fields name of fields used, if empty all fields are used
	 * @param array(string) $ignore name of form fields to ignore
	 * @return Model
	 */
	public function toModel(Model $model, $fields = array(), $ignore = array()) 
	{
		foreach($this->fieldset->children() as $formField) {
			if (!$formField instanceof FormField) continue;
			$fieldname = $formField->attributes->name;
			if (!empty($ignore) && in_array($fieldname, $ignore)) continue;
			if (!empty($fields) && !in_array($fieldname, $fields)) continue;
			if ($model->hasField($fieldname)) {
				$model->set($fieldname, $formField->value());
			}
		}
		foreach($model->belongsTo + $model->hasMany as $modelName => $config) {
			$concreteModel = $model->{$modelName};
			$fieldName = Inflector::underscore($modelName).'_'.$concreteModel->primaryKeyName;
			if ($field = $this->fieldset->childWithAttribute('name', $fieldName)) {
				$id = $field->value();
				if (!empty($id)) {
					$model->$modelName = new $config['class']((int)$id);
				} else {
					$model->$modelName = null;	
				}
			}
		}
		return $model;
	}
	
	/**
	 * Returns all names of all form fields in the form
	 * @return array(string)
	 */
	public function fieldNames() 
	{
		$fieldNames = array();
		foreach($this->fieldset->children() as $formField) {
			if (!$formField instanceof FormField) continue;
			$fieldNames[] = $formField->attributes->name;
		}
		return $fieldNames;
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exceptions
 */
class FormException extends ObjectException 
{}