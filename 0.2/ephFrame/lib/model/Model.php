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

class_exists('DBConnectionManager') or require dirname(__FILE__).'/DB/DBConnectionManager.php';
class_exists('Inflector') or require dirname(__FILE__).'/../Inflector.php';
class_exists('SelectQuery') or require dirname(__FILE__).'/DB/SelectQuery.php';
class_exists('InsertQuery') or require dirname(__FILE__).'/DB/InsertQuery.php';
class_exists('UpdateQuery') or require dirname(__FILE__).'/DB/UpdateQuery.php';
class_exists('DeleteQuery') or require dirname(__FILE__).'/DB/DeleteQuery.php';

ephFrame::loadClass('ephFrame.lib.model.ModelFieldInfo');
ephFrame::loadClass('ephFrame.lib.model.ModelStructureCache');
ephFrame::loadClass('ephFrame.lib.model.ModelBehaviorHandler');
ephFrame::loadClass('ephFrame.lib.ObjectSet');

/**
 * 	Model Class
 * 
 * 	This is the basic model class that represents a database table and the
 * 	entries in it.
 * 
 *  - includes ORM
 *  - includes Behaviors
 *  - all CRUD Operations
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 04.09.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.model
 * 	@uses DBConnectionManager
 * 	@uses SelectQuery
 * 	@uses ModelStructureCache
 */
class Model extends Object {
	
	/**
	 * 	Stores information about the columns in the database table that belong	
	 * 	to this model
	 * 	@var array(ModelFieldInfo)
	 */
	public $structure = array();
	
	/**
	 * 	Stores name of the primary field of this model, this is filled
	 * 	when the model structure is read from the database in {@link loadStructure}
	 * 	but it should be usually 'id'
	 * 	@var string
	 */
	public $primaryKeyName = 'id';

	/**
	 * 	Alias for this Model Table in DB-Queries and name for view var
	 * 	@var string
	 */
	public $name;
	
	/**
	 * 	Table that this models uses, usually automaticly generated from the
	 * 	Models name
	 * 	@var string
	 */
	public $tablename;
	
	/**
	 * 	Prefix for table names, usually set in the appmodel for every app models
	 * 	to use the same table prefix like 'eph_' or something similar.
	 * 	@var string
	 */
	public $tablenamePrefix;
	
	/**
	 *	Stores the data from the table row
	 * 	@var array(mixed)
	 */
	public $data = array();
	
	/**
	 * 	Name of the DB Configuration from {@link DB_CONFIG} that should be used
	 * 	by this model. Change the used config name with {@link useDB}
	 * 	@var string
	 */
	protected $useDBConfig = 'default';
	
	/**
	 * 	Stores an instance of a DB Object that accepts queries
	 * 	@var DB
	 */
	protected $DB;
	
	/**
	 * 	Array of validation rules for Model Properties
	 * 	@var array(string)
	 */
	public $validate = array();
	
	/**
	 *	Stores the error messages that occured during a {@link validate} process
	 * 	@var array(string)
	 */
	public $validationErrors = array();
	
	/**
	 * 	Time in seconds until the model structure read from the database will be
	 * 	re-read.
	 * 	@var integer
	 */
	protected $modelCacheTTL = DAY;
	
	/**
	 *	@var ModelStrutureCache
	 */
	protected $modelStructureCache;
	
	/**
	 *	Defalut find conditions that is used on every select query
	 * 	@var array(string)
	 */
	public $findConditions = array();
	
	/**
	 *	Default number of entries to return when no limit is passed or pagination
	 * 	is used
	 * 	@var integer
	 */
	public $perPage = null;
	
	/**
	 *	Default Order Command for every select query
	 * 	@var array(string)
	 */
	public $order = array();
	
	/**
	 * 	List of Models that this model has exactly one of, F.e Customer has one
	 * 	Address:
	 * 	<code>
	 * 					Address.id
	 * 	customer.id		Address.customer_id
	 * 	customer.name	Address.street
	 * 	</code>
	 *	@var array(string)
	 */
	public $hasOne = array();
	
	/**
	 *	@var array(string)
	 */
	public $hasMany = array();
	
	/**
	 *	List of Models that this Model belongsTo. F.e. comment belongs to User
	 * 	and BlogEntry:
	 * 	<code>
	 * 	$belongsTo = ('User', 'BlogEntry');
	 * 	</code>
	 * 
	 * 	The tables should look like this then:
	 * 	<code>
	 * 	comments.id
	 *  comments.user_id ---> users.id
	 * 	comments.blogentry_id ---> blogentries.id
	 * 	</code>
	 * 	
	 * 	@var array(string)
	 */
	public $belongsTo = array();
	
	/**
	 *	@var array(string)
	 */
	public $hasAndBelongsToMany = array();
	
	/**
	 * 	Valid model association key names
	 *	@var array(string)
	 */
	protected $associationTypes = array('hasOne', 'hasMany', 'belongsTo', 'hasAndBelongsToMany');
	
	/**
	 *	Default association depth when reading data from model
	 * 	0 = just data from this model
	 * 	1 = data from associated models
	 *  2 = data from associated models and their associated models
	 * 	@var integer
	 */
	public $depth = 1;
	
	/**
	 * 	List of Behaviors a model should load
	 * 	@var ModelBehaviorHandler
	 */
	public $behaviors = array(
		'Model'
	);
	
	/**
	 * 	Create a new Model or Model Entry
	 * 
	 * 	@param integer|array(mixed) $id
	 * 	@return Model
	 */
	public function __construct($id = null, $fieldNames = array()) {
		if (!$this->name) {
			$this->name = get_class($this);
		}
		// set db source
		$this->useDB($this->useDBConfig);
		// generate tablename if empty
		$this->tablename();
		// create structure array by reading structure from database
		$this->loadStructure();
		// merge associations of this model with associations from parent models
		foreach ($this->__parentClasses() as $parentClass) {
			$parentClassVars = get_class_vars($parentClass);
			foreach ($this->associationTypes as $associationKey) {
				if (!isset($parentClassVars[$associationKey])) continue;
				$this->__mergeParentProperty($associationKey);
			}
		}
		$this->__mergeParentProperty('behaviors');
		// initialize model behavior callbacks
		$this->behaviors = new ModelBehaviorHandler($this, $this->behaviors);
		// initialize model bindings
		$this->initAssociations($id);
		// load inital data from array data or primary id
		if (is_array($id)) {
			$this->fromArray($id, $fieldNames);
		} elseif (is_int($id)) {
			if (!$this->fromId($id)) {
				return false;
			}
		}
		$this->afterConstruct();
		$this->behaviors->call('afterConstruct');
		return $this;
	}
	
	public function afterConstruct() {
		return true;
	}
	
	/**
	 * 	Init is called by the controller after it attached this model to it
	 * 	@return boolean
	 */
	public function init() {
		return true;
	}
	
	/**
	 * 	Initates all models associations defined in $belongsTo, $hasMany and so on
	 * 	@return boolean
	 */
	protected function initAssociations($bind = true) {
		// init models associated with this model
		foreach($this->associationTypes as $associationType) {
			if (!isset($this->$associationType) || (isset($this->$associationType) && !is_array($this->$associationType))) continue;
			foreach($this->$associationType as $modelName => $config) {
				// simple notation $belongsTo = arryay('User')
				if (is_int($modelName)) {
					unset($this->{$associationType}[$modelName]);
					$modelName = $config;
					$config = array();
					$this->bind($associationType, $modelName, $config);
				} else {
					$this->bind($associationType, $modelName, $config);
				}
				// skip binding to prevent maximum method nesting (infinitive repitition)
				if (is_object($bind) && (isset($this->{get_class($bind)}) || get_class($bind) == $modelName)) {
					continue;
				}
				$modelClassname = $modelName;
				$this->{$modelName} = new $modelClassname($this);
				$this->{$modelName}->{$this->name} = $this;
				if ($associationType == 'hasMany' || $associationType == 'hasAndBelongsToMany') {
					$modelName = Inflector::plural($modelName);
					$this->{$modelName} = new $modelClassname($this);
					$this->{$modelName}->{$this->name} = $this;
				}
			}
		}
		return true;
	}
	
	/**
	 * 	Dynamicly Binds an other model to this model
	 * 
	 * 	This will remove previously made bindings.
	 *
	 * 	@param string $associationType
	 * 	@param string $modelName
	 * 	@param array(string) $config
	 * 	@throws ModelInvalidAssociationTypeException
	 * 	@throws ModelReflexiveException if you try to bin the model to itsself
	 * 	@return boolean
	 */
	public function bind($associationType, $modelName, Array $config = array()) {
		// check association type
		if (!$this->validAssociationType($associationType)) {
			throw new ModelInvalidAssociationTypeException($this, $associationType);
		}
		if (empty($modelName)) {
			return false;
		}
		if ($this->name == $modelName) {
			throw new ModelReflexiveException($this);
		}
		if (!class_exists($modelName)) {
			ephFrame::loadClass('app.lib.model.'.$modelName);
		}
		if (isset($this->{$modelName})) {
			unset($this->{$modelName});
			return;
		}
		$modelVars = get_class_vars($modelName);
		if (empty($modelVars['name'])) {
			$modelVars['name'] = $modelName;
		}
		// create default config
		$config = array_merge(array(
			'conditions' => array(),
			'count' => null,
			'order' => null,
			'foreignKey' => null,
			'associationKey' => null,
			'joinTable' => null
		), $config);
		// has and belongsToMany
		if ($associationType == 'hasAndBelongsToMany') {
			if (!isset($config['joinTable'])) {
				$joinTable = $this->tablename.'_'.Inflector::underscore(Inflector::plural($modelVars['name']), true);
				$config['joinTable'] = $joinTable;
			}
		}
		// other model’s side
		if (!isset($config['foreignKey'])) {
			switch ($associationType) {
				//$config['foreignKey'] = ucFirst($modelVars['name']).'.'.Inflector::delimeterSeperate($this->name.'_id');
				case 'belongsTo':
				case 'hasOne':
					$config['foreignKey'] = ucFirst($modelVars['name']).'.'.$modelVars['primaryKeyName'];
					break;
				case 'hasMany':
					$config['foreignKey'] = ucFirst($this->name).'.'.$this->primaryKeyName;
					break;
				case 'hasAndBelongsToMany':
					$config['foreignKey'] = $config['joinTable'].'.'.Inflector::underscore($this->name.'_'.$this->primaryKeyName, true);
					break;
			}
		}
		// my side of the join
		if (!isset($config['associationKey'])) {
			switch ($associationType) {
				//$config['associationKey'] = $this->name.'.'.$this->primaryKeyName;
				case 'belongsTo':
				case 'hasOne':
					$config['associationKey'] = $this->name.'.'.Inflector::underscore($modelVars['name'].'_'.$modelVars['primaryKeyName'], true);
					break;
				case 'hasMany':
					$config['associationKey'] = $modelVars['name'].'.'.Inflector::underscore($this->name.'_'.$this->primaryKeyName, true);
					break;
				case 'hasAndBelongsToMany':
					$config['associationKey'] = $this->name.'.'.$this->primaryKeyName;
					break;
			}
		}
		$this->{$associationType}[$modelName] = $config;
		return true;
	}
	
	/**
	 * 	Removes a binding to an other model
	 *
	 * 	<code>
	 * 	$user->unbindModel
	 * 	</code>
	 * @param unknown_type $modelName
	 */
	public function undbind($modelName) {
		if (isset($this->{$modelName})) {
			foreach($this->associationTypes as $types) {

			}
			unset($this->{$modelName});
		}
		return true;
	}
	
	/**
	 *	Return default URI to existing model detail pages
	 * 	@return string
	 */
	public function detailPageUri() {
		if (!$this->exists()) return false;
		return WEBROOT.lcfirst(get_class($this)).'/'.$this->id.'/';
	}
	
	/**
	 * 	Checks if the passed $associationType is one possible bind type for models
	 *
	 * 	@param string $associationType
	 * 	@return boolean
	 */
	protected function validAssociationType($associationType) {
		return in_array($associationType, $this->associationTypes);
	}
	
	/**
	 * 	Set the database config that should be used by this model, default is
	 * 	default. The Database Configs are created in {@link DBConfig} in /app/config/.
	 * 
	 * 	So you can dynamicly change the sources of your models, like here in this
	 * 	example:
	 * 	<code>
	 * 	$this->User->useDB('newDB');
	 * 	</code>
	 *
	 * 	@param string $dbConfigName
	 * 	@return Model
	 */
	public function useDB($dbConfigName) {
		$this->useDBConfig = $dbConfigName;
		$this->DB = DBConnectionManager::getInstance()->get($this->useDBConfig);
		return $this;
	}
	
	/**
	 *	Returns the tablename that is used for this model. If no {@link tablename}
	 * 	is set it will be generated usign the singularized lowercase name of this
	 * 	class.
	 * 	@return string
	 */
	protected function tablename() {
		if (empty($this->tablename)) {
			$this->tablename = strtolower(Inflector::underscore(Inflector::pluralize($this->name)));
		}
		if (substr($this->tablename, 0, strlen($this->tablenamePrefix)) !== $this->tablenamePrefix) {
			$this->tablename = strtolower($this->tablenamePrefix.$this->tablename);
		}
		return $this->tablename;
	}
	
	/**
	 * 	Fills the Model data from an Array ignoring all keys from $fieldNames
	 * 	
	 * 	<code>
	 * 	// filling a User but only with username and email
	 * 	$User = new User();
	 * 	$User->fromArray($_POST, array('username', 'email'));
	 * 	</code>
	 * 
	 * 	@todo check if this works with [Model][id] notation in $fieldNames
	 * 	@param array(mixed) $data
	 * 	@param array(string) name of the field that should be set (so you can ignore keys)
	 * 	@return Model
	 */
	public function fromArray(Array $data = array(), Array $fieldNames = array()) {
		foreach($data as $key => $value) {
			if (count($fieldNames) > 0 && !in_array($key, $fieldNames)) continue;
			$this->set($key, $value);
		}
		return $this;
	}
	
	/**
	 * 	Fills Model data by searching the database table for a matching primaryKey
	 * 	value
	 * 	@return boolean
	 */
	public function fromId($id) {
		//$this->set($this->primaryKeyName, (int) $id);
		if (!$model = $this->findBy($this->primaryKeyName, $id)) {
			return false;
		}
		$this->data = $model->toArray();
		return true;
	}
	
	/**
	 * 	Returns the model data as array
	 * 	@return array(mixed)
	 */
	public function toArray() {
		return $this->data;
	}
	
	/**
	 *	Update a single field of a model
	 * 
	 * 	Save a new value for one field of a model and save it emediently.
	 * 	Returns true if everything worked fine, or false if the model did not
	 * 	exists (i.e. the id was not set) or fieldname not found in this model.
	 * 
	 * 	This can be used to fast set publication status for example
	 * 	<code>
	 * 	$entry->saveField('public', true);
	 * 	</code>
	 * 	
	 * 	@param string $fieldname
	 * 	@param mixed $value
	 * 	@return boolean
	 */
	public function saveField($fieldname, $value) {
		if (!$this->exists() || !$this->hasField($fieldname)) return false;
		$this->query('UPDATE '.$this->tablename.' SET `'.$fieldname.'` = '.DBQuery::quote($value, $this->structure[$fieldname]->quoting).' WHERE '.$this->primaryKeyName.' = '.$this->get($this->primaryKeyName));
		$this->set($fieldname, $value);
		return $this->save();
	}
	
	/**
	 * 	Save Model Data to database table
	 * 
	 * 	@param boolean $validate
	 * 	@param array(string) $fieldNames
	 * 	@return boolean
	 */
	public function save($validate = true, Array $fieldNames = array()) {
		// use fieldnames to create data array that should be saved or inserted
//		$data = array();
//		if (empty($fieldNames)) {
//			$fieldNames = array_keys($this->structure);
//		}
//		foreach($fieldNames as $fieldName) {
//			if (!isset($this->structure[$fieldName]) || !isset($this->data[$fieldName])) continue;
//			$data[$fieldName] = $this->data[$fieldName];
//		}
		if (!$this->beforeSave() || !$this->behaviors->call('beforeSave')) {
			return false;
		}
		// create save query for this model
		if (!$this->exists()) {
			$this->insert();
		} else {
			$this->update();
		}
		$this->afterSave();
		$this->behaviors->call('afterSave');;
		return $this;
	}
	
	public function beforeSave() {
		if (!$this->validate($this->data)) {
			return false;
		}
		return true;
	}
	
	public function afterSave() {
		return true;
	}
	
	/**
	 * 	Insert Action
	 * 	@param array(string) $data
	 * 	@return boolean
	 */
	protected function insert() {
		if (!$this->beforeInsert() || !$this->behaviors->call('beforeInsert')) return false;
		$quotedData = array();
		foreach($this->structure as $key => $value) {
			if (!isset($this->data[$key])) continue;
			$quotedData[$key] = DBQuery::quote($this->data[$key], $this->structure[$key]->quoting);
		}
		$q = new InsertQuery($this->tablename, $quotedData);
		$this->DB->query($q);
		$this->set($this->primaryKeyName, $this->DB->lastInsertId());
		$this->afterInsert();
		$this->behaviors->call('afterInsert');
		return true;
	}
	
	public function beforeInsert() {
		return true;
	}
	
	public function afterInsert() {
		return true;
	}
	
	/**
	 * 	
	 *	@param array(string) $data
	 * 	@return unknown
	 */
	protected function update() {
		if (!$this->beforeUpdate() || !$this->behaviors->call('beforeUpdate')) return false;
		$quotedData = array();
		foreach($this->structure as $key => $value) {
			if (!isset($this->data[$key])) continue;
			$quotedData[$key] = DBQuery::quote($this->data[$key], $this->structure[$key]->quoting);
		}
		$q = new UpdateQuery($this->tablename, $quotedData, array($this->primaryKeyName => $this->data[$this->primaryKeyName]));
		$this->query($q);
		$this->afterUpdate();
		$this->behaviors->call('afterUpdate');
		return true;
	}
	
	public function beforeUpdate() {
		// set created date if there's any
		if (!$this->exists()) {
			throw new ModelEmptyPrimaryKeyException($this);
		}
		return true;
	}
	
	public function afterUpdate() {
		return true;
	}
	
	/**
	 * 	Delete model from database table
	 * 
	 * 	<code>
	 * 	$User = new User(23);
	 * 	$User->delete();
	 * 	</code>
	 * 
	 * 	@param integer|Model $id
	 * 	@return boolean
	 */
	public function delete($id = null) {
		if (is_object($id)) {
			return $id->delete();
		} elseif ($id === null) {
			if (!$this->exists()) { 
				return false;
			}
			$id = $this->{$this->primaryKeyName};
		} else {
			$id = (int) $id;
		}
		if (!$this->beforeDelete($id) || !$this->behaviors->call('beforeDelete', array($id))) return false;
		$this->DB->query(new DeleteQuery($this->tablename, array($this->primaryKeyName => $id)));
		$this->afterDelete();
		$this->behaviors->call('afterDelete');
		$this->reset();
		return true;
	}
	
	/**
	 *	Callback called before {@link delete} starts deleting, this should
	 * 	return false to stop the deleting process.
	 * 	@param integer $id
	 * 	@return true
	 */
	protected function beforeDelete($id) {
		return true;
	}
	
	/**
	 *	Called after the model data was successfully deleted, the return value
	 * 	of this is not so important.
	 * 	@return boolean.
	 */
	protected function afterDelete() {
		return true;
	}
	
	/**
	 * 	Returns true if the model is saved in the database table.
	 * 
	 * 	If you call this method with a primary key value it will search for an
	 * 	entry in the database with this primary key.
	 * 	<code>
	 * 	if ($blogEntry = $this->BlogEntry->exists($postedId)) {
	 * 		echo 'blog entry found');
	 * 	} else {
	 * 		echo 'sorry there\'s no blogEntry with that id';
	 * 	}
	 * 	</code>
	 * 
	 * 	@param integer $id
	 * 	@return boolean|Model
	 */
	public function exists($id = null) {
		if ($id !== null || func_num_args() > 0) {
			$classname = get_class($this);
			$m = new $classname((int) $id);
			if ($m->exists()) {
				return $m;
			}
			return false;
		}
		return !empty($this->data[$this->primaryKeyName]);
	}
	
	/**
	 * 	Validates the models data or the passed data using the {@link validate}
	 * 	array and returns the result as boolean. The errors occured during
	 * 	validation are stored in {@link validationErrors}.
	 * 
	 * 	Code from a controller action
	 * 	<code>
	 * 	if(!$this->User->validate($this->request->data)) {
	 * 		$this->set('errorMessages', $this->User->validationErrors);
	 * 		$this->set('success', false);
	 * 	} else {
	 * 		$this->User->fromArray($this->request->data));
	 * 		$this->User->save();
	 * 		$this->set('success', true);
	 * 	}
	 * 	</code>
	 *
	 * 	@param array(mixed) $data
	 * 	@param array(string) $fieldNames
	 * 	@return boolean
	 */
	public function validate($data = array(), $fieldNames = array()) {
		if (func_num_args() == 0) {
			$data = $this->data;
		}
		if (func_num_args() <= 1) {
			$fieldNames = array_keys($this->structure);
		}
		$this->validationErrors = array();
		// iterate over validation rules
		foreach($fieldNames as $fieldName) {
			if (!isset($data[$fieldName])) continue;
			$r = $this->validateField($fieldName, $data[$fieldName]);
			if ($r !== true) {
				$this->validationErrors[$fieldName] = $r;
			}
		}
		return (count($this->validationErrors) == 0);
	}
	
	/**
	 * 	Validates $value to the rules assigned to $fieldName
	 * 	
	 *	@param string $fieldName
	 * 	@param mixed $value
	 * 	@return boolean|string
	 */
	public function validateField($fieldName, $value) {
		if (func_num_args() == 1 && !isset($this->data[$fieldName])) {
			return false;
		}
		if (!isset($this->validate[$fieldName])) {
			return true;
		}
		$validationRules = $this->validate[$fieldName];
		$validator = new Validator($validationRules, $this);
		$r = $validator->validate($value);
		if ($r !== true) {
			return $r;
		}
		return true;
	}
	
	/**
	 * 	Creates a default select query including all associated models defined
	 * 	in {@link belongsTo}, {@link hasMany}, {@hasOne} ...
	 * 
	 * 	@param integer $depth depth of model associations to use in select query
	 * 	@return SelectQuery
	 */
	public function createSelectQuery($conditions = array(), $order = null, $offset = 0, $count = null, $depth = null) {
		if ($depth === null) {
			$depth = $this->depth;
		}
		// prepare conditions
		if ($conditions == null) {
			$conditions = array();
		} elseif (!is_array($conditions)) {
			$conditions = array($conditions);
		}
		$conditions = array_merge($this->findConditions, $conditions);
		$query = new SelectQuery();
		$query->table($this->tablename, $this->name);
		$query->where->fromArray($conditions);
		// add fields from this table
		foreach($this->structure as $fieldInfo) {
			$query->select($this->name.'.'.$fieldInfo->name, $this->name.'.'.$fieldInfo->name);
		}
		// ordering
		if ($order == null) {
			$order = array();
		}
		if (!is_array($order) && !empty($order)) {
			$order = array($order);
		}
		$order = array_merge($order, $this->order);
		if (count($order) > 0) {
			foreach($order as $orderRule) {
				// add model table alias name if missing
				if ($this->hasField(substr($orderRule, 0, strpos($orderRule, ' ')))) {
					$orderRule = $this->name.'.'.$orderRule;
				}
				$query->orderBy($orderRule);
			}
		}
		// count and limit
		if ($count !== null) {
			$query->count((int) $count);
		}
		if ($offset > 0) {
			$query->offset((int) $offset);
		}
		// belongsto / has one
		if ($depth >= 1) {
			$joinStuff = $this->hasOne + $this->belongsTo;
			foreach($joinStuff as $modelName => $config) {
				foreach($this->{$modelName}->structure as $fieldInfo) {
					$query->select($this->{$modelName}->name.'.'.$fieldInfo->name, $this->{$modelName}->name.'.'.$fieldInfo->name);
				}
				$joinConditions = $config['conditions'];
				$joinConditions[$config['associationKey']] = $config['foreignKey'];
				$query->join($this->{$modelName}->tablename, ucFirst($modelName), DBQuery::JOIN_LEFT, $joinConditions);
			}
		}
		return $query;
	}
	
	/**
	 * 	Turns a database result into a list of models and returns it
	 * 
	 * 	@param QueryResult $result
	 * 	@param boolean $justOne
	 * 	@param integer	$depth	Depth of model association depth
	 * 	@return Set
	 */
	public function createSelectResultList(QueryResult $result, $justOne = false, $depth = null) {
		if ($depth === null) {
			$depth = $this->depth;
		}
		if ($result->numRows() == 0) {
			return false;
		}
		$belongsToAndHasOne = $this->belongsTo + $this->hasOne;
		$return = new Set();
		$classname = get_class($this);
		while($modelData = $result->fetchAssoc()) {
			if (isset($modelData['use_model'])) {
				$modelClassName = ucFirst($modelData['use_model']);
			} elseif (isset($modelData[$this->name.'.use_model'])) {
				$modelClassName = ucFirst($modelData[$this->name.'.use_model']);
			} else {
				$modelClassName = $classname;
			}
			if (!class_exists($modelClassName)) {
				ephFrame::loadClass('app.lib.model.'.$modelClassName);
			}
			$model = new $modelClassName($modelData);
			// fetch associated data if detph is larger than one
			if ($depth >= 1) {
				// hasOne, belongsTo data
				foreach($belongsToAndHasOne as $modelName => $config) {
					$model->{$modelName} = new $modelName($modelData);
					$model->{$modelName}->depth = $depth-1;
				}
				// fetch hasMany associated Models
				foreach($this->hasMany as $modelName => $config) {
					$primaryKeyValue = $model->get($model->primaryKeyName);
					if (empty($primaryKeyValue)) continue;
					$associatedModelNamePlural = Inflector::plural($modelName);
					$joinConditions = array_merge($config['conditions'], array(
						$config['associationKey'] => $primaryKeyValue
					));
					if ($this->{$modelName} instanceof Model) {
						$associatedData = $this->{$modelName}->findAll($joinConditions, null, null, null, $depth - 1);
					}
					if (empty($associatedData)) {
						$associatedData = new Set();
					}
					$model->{$associatedModelNamePlural} = $associatedData;
				}
				// fetch HMBTM associated models
				foreach($this->hasAndBelongsToMany as $modelName => $config) {
					$primaryKeyValue = $model->get($model->primaryKeyName);
					if (empty($primaryKeyValue)) continue;
					$associatedModelNamePlural = Inflector::plural($modelName);
					$joinConditions = array_merge($config['conditions'], array(
						$config['foreignKey'] => $primaryKeyValue,
						$config['joinTable'].'.tag_id' => $modelName.'.id'
					));
					$q = $this->{$modelName}->createSelectQuery(null, $config['order']);
					$q->join($config['joinTable'], null, DBQuery::JOIN, $joinConditions);
					if ($this->{$modelName} instanceof Model) {
						$model->{Inflector::plural($modelName)} = $this->{$modelName}->query($q);
					};
//					foreach($this->Tags as $Tag) {
//						var_dump(get_class($Tag));
//						var_dump($Tag->toArray());
//					}
				}
			}
			$return->add($model);
			if ($justOne) break;
		}
		if ($justOne) {
			return $return[0];
		}
		return $return;
	}
	
	public function query($query, $depth = null) {
		return $this->createSelectResultList($this->DB->query($query), false, $depth);
	}
	
	/**
	 * 	Search a single Row and return it as Model
	 * 	
	 * 	<code>
	 * 	// search a single user by his mail addy
	 * 	$User->find(array('email' => 'love@ephigenia'));
	 * 	// search a single blog entry by it's primary key value
	 * 	$Comment->find(666);
	 * 	</code>
	 *
	 * 	@param string|array $conditions
	 * 	@param array $order
	 * 	@return Model|boolean
	 */
	public function find($conditions, $order = null, $depth = null) {
		$query = $this->createSelectQuery($conditions, $order, null, null, $depth);
		if (!$this->beforeFind(&$query)) return false;
		$result = $this->DB->query($query, $depth); 
		if ($resultSet = $this->createSelectResultList($result, true)) {
			return $this->afterFind($resultSet);
		}
		return false;
	}
	
	/**
	 *	Alias for {@link find}
	 * 	@param array(string)	$conditions
	 * 	@param array(string)	$order
	 * 	@param integer			$depth
	 * 	@return Model|boolean
	 */
	public function findOne($conditions, $order = null, $depth = null) {
		return $this->find($conditions, $order, $depth);
	}
	
	/**
	 *	Callback get's called before {@link find} query is send to database
	 * 	@param string $query
	 * 	@return boolean
	 */
	public function beforeFind($query) {
		return true;
	}
	
	/**
	 *	Callback called before $results are returned from the model
	 * 	@var mixed $results
	 */
	public function afterFind($results) {
		return $results;
	}
	
	/**
	 *	Tries to return a Model from the table that matches the $key => $value
	 * 	rule passed to this method. These method handles all the
	 * 	$model->getby[fieldnam] calls.<br />
	 * 	<br />
	 * 	If the model has a field named like $fieldname the $value string is
	 * 	automatically quoted with the fields appropriate quoting type. So you
	 * 	don’t need to quote user names or passwords on user logins to prevent
	 * 	SQL-Injections when using {@link findyBy}
	 * 
	 * 	<code>
	 * 	// find user by email addy
	 * 	$User->findBy('email', 'love@ephigenia.de');
	 * 	// find by primary id (just one parameter)
	 * 	$User->findBy(666);
	 * 	// find first entry where lastlogin = NULL (not filled)
	 * 	$User->findBy('lastlogin', null);
	 * 	</code>
	 * 	
	 * 	@param string 	$fieldname
	 * 	@param string 	$value
	 * 	@param integer $depth
	 * 	@return Model|boolean
	 */
	public function findBy($fieldname, $value = null, $depth = null) {
		// if no fieldname passed and just single argument use this as id
		if (func_num_args() == 1) {
			$fieldname = $this->primaryKeyName;
		}
		// quote value field
		if ($this->hasField($fieldname)) {
			$value = DBQuery::quote($value, $this->structure[$fieldname]->quoting);
			if (strpos($fieldname, '.') === false) {
				$fieldname = $this->name.'.'.$fieldname;
			}
		} else {
			$value = DBQuery::quote($value);
		}
		return $this->find(array($fieldname => $value), null, $depth);
	}
	
	/**
	 * 	Returns a {@link Set} of $conditions matching Models ordererd by $order.
	 *
	 * 	<code>
	 * 	// find all comments from one user
	 * 	$Comment->findAll(array('user_id' => 23));
	 * 	// find all entries, but 10 of them, starting at offset 5
	 * 	$Comment->findAll(null, null, 10, 5);
	 * 	// find all entries, ordered by comments_count and creation time
	 * 	$BlogEntry->findAll(null, array('creation DESC', 'comments_count DESC');
	 * 	</code>
	 * 
	 * 	@param array(string)|string $conditions
	 * 	@param array(string)|string $order
	 * 	@param integer $count Number of items to return
	 * 	@param integer $offset Offset to select from
	 * 	@return Set(Model)|boolean
	 */
	public function findAll($conditions = null, $order = null, $offset = 0, $count = null, $depth = null) {
		$query = $this->createSelectQuery($conditions, $order, $offset, $count, $depth);
		if (!$this->beforeFind($query)) return false;
		$result = $this->DB->query($query, $depth);
		if ($resultSet = $this->createSelectResultList($result, false, $depth)) {
			return $this->afterFind($resultSet);
		}
		return false;
	}
	
	/**
	 *	Returns random amount of entries from the model
	 * 	@param array(string) $conditions
	 * 	@return Set(Model)|boolean
	 */
	public function findAllRandom($conditions = null, $count = 0, $depth = null) {
		return $this->findAll($conditions, array('RAND()'), 0, $count, $depth);
	}
	
	/**
	 *	Returns a single random row from the model
	 * 	@param array(string) $conditions
	 * 	@return Model|boolean
	 */
	public function findRandom($conditions = null) {
		return $this->find($conditions, array('RAND()'));
	}
	
	/**
	 *	Returns the number of entries found
	 * 	@param array(string) $conditions
	 * 	@param integer $offset
	 * 	@param integer $count
	 * 	@return integer
	 */
	public function countAll($conditions = null, $offset = null, $count = null) {
		$query = $this->createSelectQuery($conditions, array(), $offset, $count);
		$query->select = new Hash(array('COUNT(*)' => 'count'));
		$result = $this->DB->query($query);
		if (!$result) {
			return false;
		}
		$r = $result->fetchAssoc();
		return $r['count'];
	}
	
	/**
	 *	Returns an array with information about pagination in this model
	 * 	@todo maybe this is not part of model, maybe it's more controller-like?
	 * 	@param integer $page Current Page Number
	 * 	@param integer $perPage number of items per page
	 * 	@param array(string) $conditions Conditions to mention when paginating
	 * 	@return array()
	 */
	public function paginate($page = 1, $perPage = null, $conditions = null) {
		$page = abs((int) $page); $perPage = abs((int) $perPage);
		if ($page <= 0) $page = 1;
		if ($perPage == 0) $perPage = $this->perPage;
		$total = $this->countAll($conditions);
		if (!$perPage) {
			$lastPage = 1;
		} else {
			$lastPage = ceil($total / $perPage);
		}
		return array(
			'page' => $page,
			'perPage' => $perPage,
			'next' => ($page < $lastPage) ? $page+1 : false,
			'previous' => ($page > 1) ? $page - 1 : false,
			'pages' => $lastPage,
			'pagesTotal' => $lastPage,
			'total' => $total,
			'last' => $lastPage,
			'first' => 1
		);
	}
	
	/**
	 * 	Returns all entries where $fieldname = $value. The values should be
	 * 	quoted with DBQuery::quote();
	 * 
	 * 	<code>
	 * 	// find all users whose usernames start with an a
	 * 	$User->findAllBy('SUBSTR(username, 0, 1)', 'a');
	 * 	// find all users that never logged on
	 * 	$User->findAllBy('lastlogin', NULL, false);
	 * 	// find all users that never logged in sort by creation date
	 * 	$User->findAllBy('lastlogin', NULL
	 * 	</code>
	 * 	
	 * 	@todo implement that $value can be an array and the query uses $fieldname in (item, item) ...
	 *	@param mixed $fieldname
	 * 	@param mixed $value
	 * 	@param array(string) $order
	 * 	@param integer $offset
	 * 	@param integer $count
	 * 	@param integer $depth depth of model recursion (0-2);
	 * 	@return Set(Model)|boolean
	 */
	public function findAllBy($fieldname, $value = null, $order = null, $offset = 0, $count = null, $depth = null) {
		if ($this->hasField($fieldname)) {
			$value = DBQuery::quote($value, $this->structure[$fieldname]->quoting);
		} else {
			$value = DBQuery::quote($value);
		}
		$conditions = array($fieldname => $value);
		return $this->findAll($conditions, $order, $offset, $count, $depth);
	}
	
	/**
	 * 	Method handling find[By|All|AllBy][fieldName] and
	 * 	$model->username() returns 'username' index of data if 'username' is a
	 * 	part of the table structure.
	 * 
	 * 	@param string $methodName
	 * 	@param array $args
	 */
	public function __call($methodName, Array $args) {
		// catch findAllBy[fieldname] calls
		if (preg_match('/(findAll(By)?)(.+)/i', $methodName, $found)) {
			return $this->findAllBy(Inflector::delimeterSeperate($found[3], null, true), $args[0]);
		// catch findBy[fieldname] calls 
		} elseif (preg_match('/find(By)?(.+)/i', $methodName, $found)) {
			array_unshift($args, Inflector::delimeterSeperate($found[2], null, true));
			return $this->callMethod('findBy', $args);
		// catch $model->username() calls
		} elseif (isset($this->structure[$methodName])) {
			return $this->structure[$methodName];
		} else {
			$result = $this->behaviors->__call($methodName, $args);
			if ($result !== null) {
				return $result;
			}
		}
		// all other method that could be called till here _must_ be defined
		// in this class (they would be called instead of __call), so we can
		// throw an error
		trigger_error(get_class($this).' '.$methodName.' is not defined.', E_USER_ERROR);
	}
	
	/**
	 * 	Tests if a field exists in model structure.
	 * 
	 * 	This tests if a specific field is defined for this model. This will
	 * 	work with model and without model in front of fieldname:
	 * 	<code>
	 * 	// both the same
	 * 	$user->hasField('username');
	 * 	$user->hasField('User.username');
	 * 	// but this will fail:
	 * 	$user->hasField('Something.username');
	 * 	</code>
	 * 
	 * 	@param string $fieldname
	 * 	@return boolean
	 */
	public function hasField($fieldname) {
		if (strpos($fieldname, '.') == false) {
			return isset($this->structure[trim($fieldname)]);
		} else {
			$modelName = substr($fieldname, 0, strpos($fieldname, '.')-1);
			if ($modelName !== $this->name) {
				return false;
			}
			return $this->hasField(substr($fieldname, strpos($fieldname, '.') + 1));
		}
	}
	
	/**
	 *	Tests if $fieldname field is not empty.
	 *
	 *	@param string	$fieldname
	 *	@param mixed	$default
	 *	@return boolean
	 */
	public function isEmpty($fieldname) {
		return empty($this->data[$fieldname]);
	}
	
	/**
	 *	Returns the value of a model field or a default value if the field was
	 *	empty.
	 *
	 *	<code>
	 *	echo 'URL: '.$TextEntry->get('url', 'no url given').'<br />';
	 *	</code>
	 *
	 *	You can also integrate this into boolean constructs
	 *	<code>
	 *	if ($url = $TextEntry->get('url', false)) {
	 *		echo $url;
	 *	} else {
	 *		echo 'no url given';
	 *	}
	 *	</code>
	 *	
	 * 	@param string	$fieldname
	 * 	@param mixed	$default
	 */
	public function get($fieldname, $default = null) {
		if (is_scalar($fieldname)) {
			if (isset($this->data[$fieldname])) {
				return $this->data[$fieldname];
			} elseif (empty($this->data[$fieldname]) && func_num_args() > 1) {
				return $default;
			} elseif (isset($this->structure[$fieldname])) {
				return null;
			}
		}
		user_error('undefined variable \''.$fieldname.'\' of class '.get_class($this), E_USER_ERROR);
	}
	
	public function __get($fieldname) {
		return $this->get($fieldname);	
	}
	
	/**
	 * 	Set a model field value
	 * 
	 * 	Setting a new username and email and save model
	 * 	<code>
	 * 	$user->set('username', 'Marcel Eichner');
	 * 	$user->set('email', 'love@ephigenia.de');
	 * 	$user->save();
	 * 	</code>
	 * 
	 * 	Setting values of models associated with the model
	 * 	<code>
	 * 	$user->Company->set('status', false');
	 * 	$user->Company->save();
	 * 	</code>
	 *
	 * @param unknown_type $fieldName
	 * @param unknown_type $value
	 * @return unknown
	 */
	public function set($fieldName, $value = null) {
		$modelName = $this->name;
		// catch associated model setters
		if (($pointPos = strpos($fieldName, '.')) !== false) {
			$modelName = substr($fieldName, 0, $pointPos);
			$fieldName = substr($fieldName, $pointPos+1);
		}
		// assign value to this models data
		if ($modelName == $this->name) {
			if (isset($this->structure[$fieldName])) {
				// type conversion
				if (!is_scalar($value)) {
					$value = (string) $value;
				}
				// use quoting type of structure
				switch($this->structure[$fieldName]->quoting) {
					case ModelFieldInfo::QUOTE_BOOLEAN:
						$value = (bool) $value;
						break;
					case ModelFieldInfo::QUOTE_FLOAT:
						$value = (float) $value;
						break;
					case ModelFieldInfo::QUOTE_STRING:
						$value = (string) $value;
						break;
					case ModelFieldInfo::QUOTE_INTEGER:
						$value = (int) $value;
						break;
				}
				$this->data[$fieldName] = $value;
			} elseif (is_object($value)) {
				$this->$fieldName = $value;
			} else {
				$this->data[$fieldName] = $value;
			}
		} elseif (isset($this->$modelName)) {
			$this->$modelName->set($fieldName, $value);
		}
		return $this;
	}
	
	public function __set($fieldName, $value) {
		return $this->set($fieldName, $value);
	}
	
	/**
	 * 	Reset the model data and recreate associated models
	 * 	@return boolean
	 */
	public function reset() {
		if (is_array($this->structure)) {
			foreach($this->structure as $fieldInfo) {
				$this->data[$fieldInfo->name] = null;
				if ($fieldInfo->primary) {
					$this->primaryKeyName = $fieldInfo->name;
				}
			}
		} else {
			$this->initAssociations();
		}
		return $this;
	}
	
	/**
	 * 	Loads Models structure from Database or cached Structure file into
	 * 	{@link structure} of this model.
	 * 	@return Model
	 */
	protected function loadStructure() {
//		$this->modelCacheTTL = 0;
		if (!isset($this->modelStructureCache)) {
			$this->modelStructureCache = new ModelStructureCache($this, $this->modelCacheTTL);
		}
		if (!$this->structure = $this->modelStructureCache->load()) {
			$tableInfo = $this->DB->describe($this->tablename());
			// parse table info column by column
			foreach($tableInfo as $index => $columnInfo) {
				$modelField = new ModelFieldInfo($columnInfo);
				$this->structure[$modelField->name] = $modelField;
			}
			$this->modelStructureCache->save($this->structure);
		}
		// creating all key indexes in th edata array and fill them with null
		// or their default value from the field info
		foreach($this->structure as $fieldName => $fieldInfo) {
			//if (array_key_exists($fieldName, $this->data)) continue;
			if (isset($fieldInfo->default)) {
				$this->data[$fieldName] = $fieldInfo->default;
			} elseif (!isset($this->data[$fieldName])) {
				$this->data[$fieldName] = null;
			}
		}
		return $this;
	}
	
	/**
	 * 	Returns an array of all fieldnames of this model
	 * 	@return array(string)
	 */
	public function fieldNames() {
		$fieldNames = array();
		foreach($this->structure as $fieldInfo) {
			$fieldNames[] = $fieldInfo->name;
		}
		return $fieldNames;
	}
	
}

/**
 * 	@package ephFrame
 *	@subpackage ephFrame.exception
 */
class ModelException extends ObjectException {}

/**
 * 	@package ephFrame
 *	@subpackage ephFrame.exception
 */
class ModelInvalidAssociationTypeException extends ModelException {
	public function __construct(Model $model, $associationType) {
		$messge = 'Invalid association type detected: ’'.$associationType.'’ in Model '.$model->name.' (class: '.get_class($model).')';
		parent::construct($message);
	}
}

/**
 * 	@package ephFrame
 *	@subpackage ephFrame.exception
 */
class ModelReflexiveException extends ModelException {}

/**
 *	@package ephFrame
 *	@subpackage ephFrame.exception
 */
class ModelEmptyPrimaryKeyException extends ModelException {
	public function __construct(Model $model) {
		$message = 'The primary key on '.$model->name.'.'.$model->primaryKeyName.' is empty and can not be uesed!';
		parent::__construct($message);
	}
}

?>