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

ephFrame::loadClass('ephFrame.lib.model.DB.DBConnectionManager');
ephFrame::loadClass('ephFrame.lib.Inflector');
ephFrame::loadClass('ephFrame.lib.model.DB.SelectQuery');
ephFrame::loadClass('ephFrame.lib.model.DB.InsertQuery');
ephFrame::loadClass('ephFrame.lib.model.DB.UpdateQuery');
ephFrame::loadClass('ephFrame.lib.model.DB.DeleteQuery');
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
 * 	@todo refactor this, there are to many lines!
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
	protected $primaryKeyName = 'id';

	/**
	 * 	Alias for this Model Table in DB-Queries
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
	 * 	Name of db configuration that should be used, defined in {@link DB_CONFIG}
	 * 	@var string
	 */
	protected $useDBConfig = 'default';
	
	/**
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
	protected $modelCacheTTL = HOUR;
	
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
	 *	Default Order Command for every select query
	 * 	@var array(string)
	 */
	public $order = array();
	
	public $hasOne = array();
	
	public $hasMany = array();
	
	public $belongsTo = array();
	
	public $hasAndBelongsToMany = array();
	
	protected $associationTypes = array('hasOne', 'hasMany', 'belongsTo', 'hasAndBelongsToMany');
	
	/**
	 * 	@var ModelBehaviorHandler
	 */
	public $behaviors = array('Model');
	
	/**
	 * 	Create a new Model or Model Entry
	 * 
	 * 	@param integer|array(mixed) $id
	 * 	@return Model
	 */
	public function __construct($id = null) {
		$this->name = get_class($this);
		// set db source
		$this->useDB($this->useDBConfig);
		// generate tablename if empty
		$this->tablename();
		// create structure array by reading structure from database
		$this->loadStructure();
		// merge associations of this model with associations of every parent model
		foreach ($this->__parentClasses() as $parentClass) {
			$parentClassVars = get_class_vars($parentClass);
			foreach ($this->associationTypes as $associationKey) {
				if (!isset($parentClassVars[$associationKey])) continue;
				if (is_array($parentClassVars[$associationKey])) {
					$this->$associationKey = array_unique(array_merge($parentClassVars[$associationKey], $this->$associationKey));
				}
				if (in_array($this->name, $this->$associationKey)) {
					user_error('Model '.$this->name.' can not be associated with itself', E_USER_ERROR);
				}
			}
			if (isset($parentClassVars['behaviors']) && is_array($parentClassVars['behaviors'])) {
				$this->behaviors = array_unique(array_merge($parentClassVars['behaviors'], $this->behaviors));		
			}
		}
		// initialize model behavior callbacks
		$this->behaviors = new ModelBehaviorHandler($this, $this->behaviors);
		// initialize model bindings
		$this->initAssociations();
		// load inital data from array data or primary id
		if (is_array($id)) {
			$this->fromArray($id);
		} elseif (is_int($id)) {
			$this->fromId($id);
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
	protected function initAssociations() {
		// init models associated with this model
		foreach($this->associationTypes as $associationType) {
			if (!isset($this->$associationType) || (isset($this->$associationType) && !is_array($this->$associationType))) continue;
			foreach($this->$associationType as $modelName => $associationConfig) {
				// simple association ($belongsTo = array('User', 'Company');
				if (!is_array($associationConfig)) {
					$modelName = $associationConfig;
					$associationConfig = array();
				}
				if (!isset($associationConfig['conditions'])) {
					$associationConfig['conditions'] = array();
				}
				$this->bind($associationType, $modelName, $associationConfig);
			}
		}
		return true;
	}
	
	/**
	 * 	Dynamicly Binds an other model to this model
	 *
	 * 	@param string $associationType
	 * 	@param string $modelName
	 * 	@param array(string) $associationConfig
	 * 	@throws ModelInvalidAssociationTypeException
	 * 	@throws ModelReflexiveException if you try to bin the model to itsself
	 * 	@return boolean
	 */
	public function bind($associationType, $modelName, Array $associationConfig = array()) {
		if (!$this->validAssociationType($associationType)) {
			throw new ModelInvalidAssociationTypeException($this, $associationType);
		}
		if (empty($modelName)) {
			return false;
		}
		if ($this->name == $modelName) {
			throw new ModelReflexiveException($this);
		}
		// load Model class (also external paths are enabled, such as 'vendor.cms.model.User')
		if (strpos($modelName, '.') === false) {
			if (!class_exists($modelName)) {
				ephFrame::loadClass('app.lib.model.'.$modelName);
			}
		} else {
			$modelClassname = ClassPath::className($modelName);
			if (!class_exists($modelClassname)) {
				ephFrame::loadClass($modelName); 
			}
			$modelName = $modelClassname;
		}
		// don't double bind
		if (!isset($this->$modelName)) {
			$this->{$modelName} = new $modelName();
			$this->{$modelName}->{$this->name} = $this;
		}
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
			unset($this->{$modelName});
		}
		return true;
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
	 * 	Fills the model with data from an array
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
	 * 	Returns an array containing
	 * 	@return array(mixed)
	 */
	public function toArray() {
		return $this->data;
	}
	
	/**
	 * 	Delete a model entry
	 * 	
	 * 	You can delete model entries on 3 different kind of ways. Delete the
	 * 	current model entry, model entry by primary id and model as parameter:
	 * 	<code>
	 * 
	 * 	</code>
	 * 	@param integer|model $id
	 */
	public function delete($id = null) {
		if (($id == null || func_num_args() == 0)) {
			if (empty($this->data[$this->primaryKeyName])) {
				return $this;
			}
			$id = $this->{$this->primaryKeyName};
		} elseif (is_object($id)) {
			return $id->delete();
		} else {
			$id = (int) $id;
		}
		if (!$this->beforeDelete() || !$this->behaviors->call('beforeDelete')) {
			return false;
		}
		$q = new DeleteQuery($this->tablename, array($this->primaryKeyName => DBQuery::quote($id, $this->structure[$this->primaryKeyName]->quoting)));
		$this->DB->query($q);
		// clear model data
		foreach($this->structure as $columnName => $columnInfo) {
			$this->data[$columnName] = $this->structure[$columnName]->default;
		}
		$this->afterDelete();
		$this->behaviors->call('afterDelete');
		return $this;
	}
	
	protected function beforeDelete() {
		return true;
	}
	
	protected function afterDelete() {
		return true;
	}
	
	/**
	 * 	Save the current state of this model
	 * 	@param Model $model
	 * 	@param boolean $validate
	 * 	@param array(string) $fieldNames
	 * 	@return boolean
	 */
	public function save(Model $model = null, $validate = true, Array $fieldNames = array()) {
		// use fieldnames to create data array that should be saved or inserted
		$data = array();
		if (empty($fieldNames)) {
			$fieldNames = array_keys($this->structure);
		}
		foreach($fieldNames as $fieldName) {
			if (!isset($this->structure[$fieldName]) || !isset($this->data[$fieldName])) continue;
			$data[$fieldName] = $this->data[$fieldName];
		}
		if (!$this->beforeSave(&$data, $fieldNames) || !$this->behaviors->call('beforeSave', &$data, $fieldNames)) {
			return false;
		}
		// create save query for this model
		if (!$this->exists()) {
			$this->insert($data);
		} else {
			$this->update($data);
		}
		$this->afterSave();
		$this->behaviors->call('afterSave');;
		return $this;
	}
	
	public function beforeSave(Array $data = array(), Array $fieldNames = array()) {
		if (!$this->validate($data, $fieldNames)) {
			return false;
		}
		return true;
	}
	
	public function afterSave() {
		return true;
	}
	
	/**
	 * 	@param array(string) $data
	 * 	@return boolean
	 */
	protected function insert(Array $data = array()) {
		if (!$this->beforeInsert(&$data) || !$this->behaviors->call('beforeSave', &$data)) {
			return false;
		}
		foreach($data as $key => $value) {
			$quotedData[$key] = DBQuery::quote($value, $this->structure[$key]->quoting);
		}
		// set created date if there's any
		if (isset($this->structure['created']) && !isset($quotedData['created'])) {
			if ($this->structure['created']->quoting == ModelFieldInfo::QUOTE_STRING) {
				// @todo set time string depending on sql type
				$quotedData['created'] = 'NOW()';
			} elseif($this->structure['created']->quoting == ModelFieldInfo::QUOTE_INTEGER) {
				$quotedData['created'] = 'UNIX_TIMESTAMP()';
			}
		}
		$q = new InsertQuery($this->tablename, $quotedData);
		$this->DB->query($q);
		$this->data[$this->primaryKeyName] = $this->DB->lastInsertId();
		return true;
	}
	
	public function beforeInsert(Array $data = array()) {
		return true;
	}
	
	/**
	 * 	
	 *	@param array(string) $data
	 * 	@return unknown
	 */
	protected function update(Array $data = array()) {
		if (!$this->beforeUpdate(&$data) || !$this->behaviors->call('beforeUpdate', &$data)) {
			return false;
		}
		if (!$this->exists()) {
			throw new ModelEmptyPrimaryKeyException($this);
		}
		foreach($data as $key => $value) {
			$quotedData[$key] = DBQuery::quote($value, $this->structure[$key]->quoting);
		}
		// set created date if there's any
		if (isset($this->structure['updated']) && isset($data['updated'])) {
			if ($this->structure['updated']->quoting == ModelFieldInfo::QUOTE_STRING) {
				// @todo set time string depending on sql type
				$quotedData['updated'] = 'NOW()';
			} elseif($this->structure['created']->quoting == ModelFieldInfo::QUOTE_INTEGER) {
				$quotedData['updated'] = 'UNIX_TIMESTAMP()';
			}
		}
		$q = new UpdateQuery($this->tablename, $quotedData, array($this->primaryKeyName => $this->data[$this->primaryKeyName]));
		$this->DB->query($q);
		return true;
	}
	
	public function beforeUpdate($data = array()) {
		return true;
	}
	
	/**
	 * 	This method will if this model data exists in the db
	 * 
	 * 	You can use this method for checking your model class for existence in 
	 * 	the database. If no parameter is found it's just checked if the primary
	 * 	key is filled.
	 * 	<code>
	 * 	// if the User was found we get a valid user and can chek it
	 * 	if ($user = $this->User->findById(24)) {
	 * 		var_dump($user->exists);	// should turn out in true
	 * 	} else {
	 * 		echo 'user not found';
	 * 	}
	 * 	</code>
	 * 
	 * 	If you call this method with a primary key value it will search for an
	 * 	entry in the database with this primary key.
	 * 	<code>
	 * 	if ($this->BlogEntry->exists($postedId)) {
	 * 		echo 'sorry no blog entry found with this id!';
	 * 	} else {
	 * 		// go on posting the comment or what ever
	 * 	}
	 * 	</code>
	 * 
	 * 	@param integer $id
	 * 	@return boolean
	 */
	public function exists($id = null) {
		if ($id !== null) {
			$query = new SelectQuery();
			$query->table($this->tablename())->where($this->primaryKeyName, (int) $id);
			$result = $this->DB->query($query);
			if ($result->numRows() >= 0) {
				return true;
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
		$this->validationErrors = array();
		foreach($fieldNames as $fieldName) {
			if (!isset($data[$fieldName])) continue;
			$r = $this->validateField($fieldName, $data[$fieldName]);
			if ($r !== true) {
				$this->validationErrors[$fieldName] = $r;
			}
		}
		if (!empty($this->validationErrors)) {
			return false;
		}
		return true;
	}
	
	/**
	 *	@param string $fieldName
	 * 	@param mixed $value
	 * 	@return boolean|string
	 */
	public function validateField($fieldName, $value) {
		// validation of fields that don't exists are false
		if (func_num_args() == 1 && !isset($this->data[$fieldName])) {
			return false;
		}
		// no validation rules on fields are always ok
		if (!isset($this->validate[$fieldName])) {
			return true;
		}
		// use validation config to validate value
		// @todo add validator as class memmber $this->validator->validate();
		$config = $this->validate[$fieldName];
		$validator = new Validator($config, $this);
		return $validator->validate($value);
	}
	
	/**
	 * 	Creates a default select query including all associated models defined
	 * 	in {@link belongsTo}, {@link hasMany}, {@hasOne} ...
	 * 
	 * 	@todo FIXME export the join conditions to extra methods!!!
	 * 	@todo FIXME export foreign key and table name methods from this!!!
	 * 	@param integer $depth depth of model associations to use in select query
	 * 	@return SelectQuery
	 */
	protected function createSelectQuery($depth = null, $conditions = array(), $order = array(), $count = null) {
		foreach(array('conditions', 'order') as $__key) {
			if (empty(${$__key})) {
				${$__key} = array();
			} elseif (!is_array(${$__key})) {
				${$__key} = array(${$__key});
			}
		}
		// use standard find conditions
		$conditions = array_merge($this->findConditions, $conditions);
		$q = new SelectQuery();
		$q->table($this->tablename(), $this->name);
		// add fields from this table
		foreach($this->structure as $fieldInfo) {
			$q->select($this->name.'.'.$fieldInfo->name.' as \''.$this->name.'.'.$fieldInfo->name.'\'');
		}
		// belongsTo
		if (($depth > 0 || $depth == null)) {
			$thisSide = $this->name.'.'.$this->primaryKeyName;
			// belongsTo 
			if (!empty($this->belongsTo)) { 
				foreach($this->belongsTo as $modelName => $associationData) {
					$joinConditions = array();
					if (!is_array($associationData)) {
						$modelName = $associationData;
					} elseif (isset($associationData['conditions'])) {
						$joinConditions = $associationData['conditions'];
					}
					$foreignKey = Inflector::underscore($this->name, true).'_'.$this->primaryKeyName;
					$joinConditions[$thisSide] = $this->$modelName->name.'.'.$foreignKey;
					$q->join($this->$modelName->tablename(), $this->$modelName->name, DBQuery::JOIN_LEFT, $joinConditions);
					foreach($this->$modelName->structure as $fieldInfo) {
						$q->select($this->$modelName->name.'.'.$fieldInfo->name.' as \''.$this->$modelName->name.'.'.$fieldInfo->name.'\'');
					}
					// join deeper models too
					if ($depth == null || $depth >= 2) {
						foreach($this->{$modelName}->belongsTo as $index => $associationData) {
							$m = $associationData;
							$belongsToTableAlias = $this->{$modelName}->{$m}->name;
							$belongsToTableName = $this->{$modelName}->{$m}->tablename;
							$foreignKey = $this->{$modelName}->name.'.'.Inflector::underscore($this->{$modelName}->{$m}->name, true).'_'.$this->{$modelName}->primaryKeyName;
							$belongsToConditions = array(
								$belongsToTableAlias.'.'.$this->{$modelName}->{$m}->primaryKeyName => $foreignKey
							);
							$q->join($belongsToTableName, $belongsToTableAlias, DBQuery::JOIN_LEFT, $belongsToConditions);	
						}
					}
				}
			}
			// hasMany
			if (!empty($this->hasMany)) {
				foreach($this->hasMany as $modelName => $associationData) {
					$joinConditions = array();
					if (!is_array($associationData)) {
						$modelName = $associationData;
					} elseif(isset($associationData['conditions'])) {
						$joinConditions = $associationData['conditions'];
					}
					$foreignKey = strtolower(Inflector::underscore($this->name).'_'.$this->primaryKeyName);
					$joinConditions[$thisSide] = $this->$modelName->name.'.'.$foreignKey;
					$q->join($this->$modelName->tablename(), $this->$modelName->name, DBQuery::JOIN_LEFT, $joinConditions);
					foreach($this->$modelName->structure as $fieldInfo) {
						$q->select($this->$modelName->name.'.'.$fieldInfo->name.' as \''.$this->$modelName->name.'.'.$fieldInfo->name.'\'');
					}
				}
			}
		}
		// add where conditions
		// @todo increase query performance and order if conditions that belong into the join go to the join and not to the where
		foreach($conditions as $left => $right) {
			$q->where($left, $right);
		}
		// add other stuff
		if (!is_array($order) && !empty($order)) {
			$order = array($order);
		}
		$order = array_merge($this->order, $order);
		if (count($order) > 0) {
			foreach($order as $orderRule) {
				$q->orderBy($orderRule);
			}
		}
		if ($count !== null) {
			$q->count($count);
		}
//		if ($this->name == 'Contact') {
//			die('<pre>'.$q.'</pre>');
//		}
		//die($q);
		return $q;
	}
	
	/**
	 * 	Turns a database result into a list of models
	 * 	@param QueryResult $result
	 * 	@return Set
	 */
	protected function createSelectResultList(QueryResult $result, $justOne = false) {
		// @todo check performance if we use ObjectCollection here
		if ($result->numRows() == 0) {
			return false;
		}
		$classname = get_class($this);
		$return = new Set();
		$lastArr = array();
		$primIdName = $this->name.'.'.$this->primaryKeyName;
		$i = 0;
		while($arr = $result->fetchAssoc()) {
			if (@$lastArr[$primIdName] != @$arr[$primIdName]) {
				if ($i > 0 && $justOne) {
					return $model;
				}
				$model = new $classname($arr);
				// add associations
				foreach($this->belongsTo as $modelName) {
					$model->{Inflector::plural($modelName)} = new ObjectSet($modelName);
				}
				foreach($this->hasOne as $modelName) {
					$model->$modelName = new $modelName($arr);
				}
			}
			foreach($this->belongsTo as $modelName) {
				// check if there's data for the associated model in the result
				$associatedModelIdFromResult = $arr[$this->$modelName->name.'.'.$this->$modelName->primaryKeyName];
				if ($associatedModelIdFromResult !== null) {
					$model->{Inflector::plural($modelName)}->add(new $modelName($arr));
				}
			}
			if (@$lastArr[$primIdName] != @$arr[$primIdName] && $i > 0 || !isset($lastArr[$primIdName])) {
				$return->add($model);
			}
			$lastArr = $arr;
			$i++;
		}
		if (!isset($model)) {
			return false;
		} else {
			if ($justOne) {
				return $model;
			}
			//$return->add($model);
		}
		if ($this->name == 'Contact') {
			var_dump($this->toArray());
			die();
		}
		//die(var_dump($return->toArray()));
		return $return;
	}
	
	/**
	 * 	Returns all elements stored in this model
	 * 	
	 * 	@param $depth integer
	 * 	@param $conditions array(string)
	 * 	@param $order array(string)
	 * 	@param $count integer
	 * 	@return Set	A Set containing Models
	 */
	public function getAll($depth = null, $conditions = array(), $order = array(), $count = null) {
		if (empty($order) && isset($this->structure['created'])) {
			$order = array($this->name.'.created DESC');
		}
		$query = $this->createSelectQuery($depth, $conditions, $order, $count);
		$result = $this->DB->query($query);
		return $this->createSelectResultList($result, false);
	}
	
	/**
	 * 	Returns matching Model Values that match $fieldname = $value
	 * 	
	 * 	Find all users with the username 'Ephigenia', in controller:
	 * 	<code>
	 * 	while($user = $this->User->findBy('username', 'Ephigenia')) {
	 * 		echo $user->name;
	 * 	}
	 * 	if (!$user) {
	 * 		echo 'no users found with that name';
	 * 	}
	 * 	</code>
	 * 
	 * 	@param string $fieldname
	 * 	@param string $value
	 * 	@return Set|boolean
	 */
	public function findOne($fieldname, $value) {
		if (strpos($fieldname, '.') === false) {
			$fieldname = $this->name.'.'.$fieldname;
		}
		$query = $this->createSelectQuery(null, array($fieldname => DBQuery::quote($value)));
		$result = $this->DB->query($query);
		return $this->createSelectResultList($result, true);
	}
	
	/**
	 * 	This will return every entry from the model with a matching
	 * 	$fieldname = $value 
	 *
	 * 	@param string $fieldname
	 * 	@param string $value
	 * 	@return Set|boolean
	 */
	public function findAll($fieldname, $value) {
		if (strpos($fieldname, '.') === false) {
			$fieldname = $this->name.'.'.$fieldname;
		}
		return $this->getAll(array($conditions => $value));
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
		// catch findBy[fieldname] calls
		if (preg_match('/(findAll(By)?)(.*)/i', $methodName, $found)) {
			$args[-1] = lcfirst($found[3]);
			return $this->callMethod('findAll', $args);
			//return $this->findAll($found[3], $args[0]);
		} elseif (preg_match('/find(By)?(.*)/i', $methodName, $found)) {
			return $this->findOne(lcfirst($found[2]), $args[0]);
		// catch $model->username() calls
		} elseif (isset($this->structure[$methodName])) {
			return $this->structure[$methodName];
		}
		// all other method that could be called till here _must_ be defined
		// in this class (they would be called instead of __call), so we can
		// throw an error
		trigger_error(get_class($this).' '.$methodName.' is not defined.', E_USER_ERROR);
	}
	
	/**
	 * 	Tests if a model has a specific field in his structure
	 * 
	 * 	@param string $fieldname
	 * 	@return boolean
	 */
	public function hasField($fieldname) {
		return isset($this->structure[trim($fieldname)]);
	}
	
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
			} else {
				$this->$fieldName = $value;
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
		}
		$this->initAssociations();
		return $this;
	}
	
	/**
	 * 	Loads Models structure from Database or cached Structure file into
	 * 	{@link structure} of this model.
	 * 	@return Model
	 */
	protected function loadStructure() {
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
		$this->reset();
		return $this;
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
		parent::__construct('Invalid assocation type \''.$associationType.'\' in Model '.$model->name. ' (class: '.get_class($model).')');
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
		parent::_-construct('This model has no primary Key value and can not be updated.');
	}
}

?>