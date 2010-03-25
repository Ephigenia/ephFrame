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

$___d = dirname(__FILE__);
class_exists('DBConnectionManager') or require $___d.'/DB/DBConnectionManager.php';
class_exists('Inflector') or require $___d.'/../Inflector.php';
class_exists('SelectQuery') or require $___d.'/DB/SelectQuery.php';
class_exists('InsertQuery') or require $___d.'/DB/InsertQuery.php';
class_exists('UpdateQuery') or require $___d.'/DB/UpdateQuery.php';
class_exists('DeleteQuery') or require $___d.'/DB/DeleteQuery.php';
class_exists('ModelFieldInfo') or require $___d.'/ModelFieldInfo.php';
class_exists('ModelStructureCache') or require $___d.'/ModelStructureCache.php';
class_exists('ModelBehaviorHandler') or require $___d.'/ModelBehaviorHandler.php';
class_exists('ObjectSet') or require $___d.'/../ObjectSet.php';
unset($___d);

/**
 * Model Class
 * 
 * This is the basic model class that represents a database table and the
 * entries in it.
 * 
 * - includes ORM
 * - includes Behaviors
 * - all CRUD Operations
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 04.09.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.model
 * @uses DBConnectionManager
 * @uses SelectQuery
 * @uses ModelStructureCache
 */
class Model extends Object 
{
	/**
	 * Stores information about the columns in the database table that belong	
	 * to this model
	 * @var array(ModelFieldInfo)
	 */
	public $structure = array();
	
	/**
	 * Stores name of the primary field of this model, this is filled
	 * when the model structure is read from the database in {@link loadStructure}
	 * but it should be usually 'id'
	 * @var string
	 */
	public $primaryKeyName = 'id';

	/**
	 * Alias for this Model Table in DB-Queries and name for view vars
	 * @var string
	 */
	public $name;
	
	/**
	 * Table that this models uses, usually automaticly generated from the
	 * Models name, set this to false if the model does not use any table
	 * @var string
	 */
	public $tablename;
	
	/**
	 * Prefix for table names, usually set in the appmodel for every app models
	 * to use the same table prefix like 'eph_' or something similar.
	 * @var string
	 */
	public $tablenamePrefix;
	
	/**
	 * Stores the data from the table row
	 * @var array(mixed)
	 */
	public $data = array();
	
	/**
	 * stores an instance of a DB Access object after model initializisation
	 * @var DB
	 */
	public $DB;
	
	/**
	 * Name of the DB Configuration from {@link DB_CONFIG} that should be used
	 * by this model. Change the used config name with {@link useDB}
	 * @var string
	 */
	protected $useDBConfig = 'default';
	
	/**
	 * Array of validation rules for Model Properties
	 * @var array(string)
	 */
	public $validate = array();
	
	/**
	 * Stores the error messages that occured during a {@link validate} process
	 * @var array(string)
	 */
	public $validationErrors = array();
	
	/**
	 * Time in seconds until the model structure read from the database will be
	 * re-read.
	 * @var integer
	 */
	protected $modelCacheTTL = DAY;
	
	/**
	 * @var ModelStrutureCache
	 */
	protected $modelStructureCache;
	
	/**
	 * Defalut find conditions that is used on every select query
	 * @var array(string)
	 */
	public $findConditions = array();
	
	/**
	 * Default number of entries to return when no limit is passed or pagination
	 * is used
	 * @var integer
	 */
	public $perPage = null;
	
	/**
	 * Default Order Command for every select query
	 * @var array(string)
	 */
	public $order = array();
	
	/**
	 * List of Models that this model has exactly one of, F.e Customer has one
	 * Address:
	 * <code>
	 * 				Address.id
	 * customer.id		Address.customer_id
	 * customer.name	Address.street
	 * </code>
	 * @var array(string)
	 */
	public $hasOne = array();
	
	/**
	 * @var array(string)
	 */
	public $hasMany = array();
	
	/**
	 * List of Models that this Model belongsTo. F.e. comment belongs to User
	 * and BlogEntry:
	 * <code>
	 * $belongsTo = ('User', 'BlogEntry');
	 * </code>
	 * 
	 * The tables should look like this then:
	 * <code>
	 * comments.id
	 * comments.user_id ---> users.id
	 * comments.blogentry_id ---> blogentries.id
	 * </code>
	 * 
	 * @var array(string)
	 */
	public $belongsTo = array();
	
	/**
	 * @var array(string)
	 */
	public $hasAndBelongsToMany = array();
	
	/**
	 * Valid model association key names
	 * @var array(string)
	 */
	protected $associationTypes = array(
		'hasOne',
		'hasMany',
		'belongsTo',
		'hasAndBelongsToMany',
	);
	
	/**
	 * Default association depth when reading data from model
	 * 0 = just data from this model
	 * 1 = data from associated models
	 * 2 = data from associated models and their associated models
	 * @var integer
	 */
	public $depth = 1;
	
	/**
	 * List of Behaviors a model should load
	 * @var ModelBehaviorHandler
	 */
	public $behaviors = array(
		'Model',
		'Timestampable',
	);
	
	/**
	 * List of models used by this model but not directly connected with it
	 * by any association.
	 * @var string
	 */
	public $uses = array();
	
	/**
	 * Create a new Model or Model Entry
	 * 
	 * @param integer|array(mixed) $id
	 * @return Model
	 */
	public function __construct($id = null, $fieldNames = array()) 
	{
		if ($this->tablename !== false) {
			if (empty($this->name)) {
				$this->name = get_class($this);
			}
			$this->tablename();
		}
		
		// get model alias name stored in $this->name
		if (is_string($fieldNames)) {
			$this->name = $fieldNames;
		}
		
		// get DB Instance if tablename is set
		if ($this->tablename !== false) {
			$this->DB = DBConnectionManager::getInstance()->get($this->useDBConfig);
			// create structure array by reading structure from database
			$this->loadStructure();
		}
		
		// merge associations of this model with associations from parent models
		foreach ($this->associationTypes as $associationKey) {
			$this->__mergeParentProperty($associationKey);
		}
		$this->__mergeParentProperty('behaviors');
		$this->__mergeParentProperty('uses');
		
		// call afterconstruct on model and behaviors
		$this->afterConstruct();
		
		// initialize model bindings
		$this->initAssociations($id);
		
		// initialize model behaviors
		$this->behaviors = new ModelBehaviorHandler($this, $this->behaviors);
		$this->behaviors->call('afterConstruct');
		// load inital data from array data or primary id
		if (is_array($id)) {
			$this->fromArray($id, is_array($fieldNames) ? $fieldNames : array());
		} elseif (is_int($id) && !$this->fromId($id)) {
			return false;
		}
		return $this;
	}
	
	/**
	 * callback called after model finished constructing and init
	 */
	public function afterConstruct() 
	{
		return true;
	}
	
	/**
	 * Init is called by the controller after it attached this model to it
	 * @return boolean
	 */
	public function init() 
	{
		return true;
	}
	
	/**
	 * Initates all models associations defined in $belongsTo, $hasMany and so on
	 * @return boolean
	 */
	protected function initAssociations($bind = true)
	{
		// init models associated with this model
		foreach($this->associationTypes as $associationType) {
			if (!is_array($this->$associationType)) continue;
			foreach($this->$associationType as $modelAlias => $config) {
				if (is_int($modelAlias)) {
					unset($this->{$associationType}[$modelAlias]);
					$modelAlias = $config;
					$config = array();
				}
				if (in_array($associationType, array('hasMany', 'hasAndBelongsToMany'))) {
					$this->{Inflector::plural($modelAlias)} = new IndexedArray();
				}
				$this->bind($modelAlias, $associationType, $config, $bind);
			}
		}
		// add models from uses array
		if (is_array($this->uses) && count($this->uses) > 0) {
			foreach($this->uses as $modelName) {
				$this->bind($modelName);
			}
		}
		return true;
	}
	
	/**
	 * Dynamicly Binds an other model to this model
	 * 
	 * This will remove previously made bindings.
	 *
	 * @param string $modelAlias
	 * @param string $associationType
	 * @param array(string) $config
	 * @throws ModelInvalidAssociationTypeException
	 * @throws ModelReflexiveException if you try to bin the model to itsself
	 * @return boolean
	 */
	public function bind($modelAlias, $associationType = null, Array $config = array(), $bind = false) 
	{
		// input-check
		if (empty($modelAlias)) return false;
		if (!empty($associationType) && !$this->validAssociationType($associationType)) throw new ModelInvalidAssociationTypeException($this, $associationType);
		// custom class name specified in config
		$classname = empty($config['class']) ? $modelAlias : $config['class'];
		// load model class if not allready loaded
		if (!class_exists($classname)) {
			if (strpos($classname, '.')) {
				$classname = ephFrame::loadClass($classname);
			} else {
				ephFrame::loadClass('app.lib.model.'.$classname);
			}
		}
		// prevent unlimited nesting
		if (is_object($bind)
			&& (
				get_class($bind) == $modelAlias
				|| $bind->name == $modelAlias
				|| isset($this->{$modelAlias})
				|| isset($bind->{$modelAlias})
			)) {
			$this->{$modelAlias} = $bind;
		} else {
			$this->{$modelAlias} = new $classname($this, $modelAlias);
		}
		$this->{$modelAlias}->{$this->name} = $this;
		$this->{$modelAlias}->{$this->name}->name = $this->name;
		
		// create default config
		$config = array_merge(array(
			'conditions' => array(),
			'count' => null,
			'order' => null,
			'foreignKey' => null,
			'associationKey' => null,
			'joinTable' => null,
			'dependent' => false,
			'class' => $classname,
			'with' => false,
		), $config);
		// has and belongsToMany
		switch($associationType) {
			case 'hasAndBelongsToMany':
				if (!isset($config['joinTable'])) {
					$config['joinTable'] = $this->tablename.'_'.Inflector::underscore(Inflector::plural($this->{$modelAlias}->name), true);
				}
				$config['joinTable'] = String::prepend($config['joinTable'], $this->tablenamePrefix, true);
				if (empty($config['with'])) {
					$config['with'] = $this->name.$modelAlias;
				}
				break;
		}
		// other model’s side
		if (!isset($config['foreignKey'])) {
			switch ($associationType) {
				case 'belongsTo':
					$config['foreignKey'] = ucFirst($this->{$modelAlias}->name).'.'.$this->{$modelAlias}->primaryKeyName;
					break;
				case 'hasOne':
					$config['foreignKey'] = ucFirst($this->{$modelAlias}->name).'.'.Inflector::delimeterSeperate($this->name.'_id', '_');
					break;
				case 'hasMany':
					$config['foreignKey'] = ucFirst($this->name).'.'.$this->primaryKeyName;
					break;
				case 'hasAndBelongsToMany':
					$config['foreignKey'] = Inflector::underscore($this->name.'_'.$this->primaryKeyName);
					break;
			}
		// add model name to foreignkeys with no model name like user_id
		} elseif (strpos($config['foreignKey'], '.') === false && $associationType !== 'hasAndBelongsToMany') {
			$config['foreignKey'] = $modelAlias.'.'.$config['foreignKey'];
		}
		// my side of the join
		if (!isset($config['associationKey'])) {
			switch ($associationType) {
				case 'belongsTo':
					$config['associationKey'] = $this->name.'.'.Inflector::underscore($modelAlias.'_'.$this->{$modelAlias}->primaryKeyName);
					break;
				case 'hasOne':
					$config['associationKey'] = $this->name.'.'.$this->primaryKeyName;
					break;
				case 'hasAndBelongsToMany':
					$config['associationKey'] = $this->name.'.'.$this->primaryKeyName;
					break;
				case 'hasMany':
					$config['associationKey'] = Inflector::underscore($this->name.'_'.$this->primaryKeyName);
					break;
			}
		} elseif (strpos($config['associationKey'], '.') === false) {
			$config['associationKey'] = $this->name.'.'.$config['associationKey'];
		}
		if (!empty($associationType)) {
			$this->{$associationType}[$modelAlias] = $config;
		}
		return true;
	}
	
	/**
	 * Removes bindings with $modelName or multiple models
	 * <code>
	 * $user->unbind('Node', 'BlogPost');
	 * </code>
	 * You can also unbind all models by calling
	 * <code>
	 * $user->unbind('all');
	 * </code>
	 * @param string|array(string) $modelName
	 * @return boolean
	 */
	public function unbind($modelName) 
	{
		if ($modelName == 'all') {
			$modelName = array_merge(
				array_keys($this->hasOne),
				array_keys($this->belongsTo),
				array_keys($this->hasMany),
				array_keys($this->hasAndBelongsToMany)
			);
		}
		if (is_array($modelName)) {
			$modelNames = $modelName;
		} else {
			$modelNames = func_get_args();
		}
		foreach($modelNames as $modelName) {
			if (!property_exists($this, $modelName)) continue;
			unset($this->{$modelName});
			unset($this->belongsTo[$modelName]);
			unset($this->hasMany[$modelName]);
			unset($this->hasOne[$modelName]);
			unset($this->hasAndBelongsToMany[$modelName]);
		}
		return true;
	}
	
	/**
	 * Return default URI to existing model detail pages
	 * @return string
	 */
	public function detailPageUri(Array $params = array()) 
	{
		if (!$this->exists()) return false;
		if (!$uri = Router::getRoute($this->name.'Id', array_merge($params, array('id' => $this->id)))) {
			$uri = WEBROOT.lcfirst($this->name).'/'.$this->id.'/';	
		}
		return $uri;
	}
	
	public function detailPageURL() 
	{
		return trim(Registry::get('WEBROOT_URL'), '/').$this->detailPageUri();
	}
	
	/**
	 * Returns unique id string for a model entry
	 * @param integer $length
	 * @return string
	 */
	public function uniqueId($length = 8) 
	{
		return substr(md5(SALT.$this->id), 0, $length);
	}
	
	/**
	 * Alias for {@link detailPageUri}
	 * @return string
	 */
	public function permaLink() 
	{
		return $this->detailPageUri();
	}
	
	/**
	 * Checks if the passed $associationType is one possible bind type for models
	 *
	 * @param string $associationType
	 * @return boolean
	 */
	protected function validAssociationType($associationType)
	{
		return in_array($associationType, $this->associationTypes);
	}
	
	/**
	 * Returns the tablename that is used for this model. If no {@link tablename}
	 * is set it will be generated usign the singularized lowercase name of this
	 * class.
	 * @return string
	 */
	protected function tablename()
	{
		if (empty($this->tablename) && $this->tablename !== false) {
			$this->tablename = strtolower(Inflector::underscore(Inflector::pluralize($this->name)));
		}
		// add table prefix name
		$this->tablename = String::prepend($this->tablename, $this->tablenamePrefix, true);
		return $this->tablename;
	}
	
	/**
	 * Fills the Model data from an Array ignoring all keys from $fieldNames
	 * 
	 * <code>
	 * // filling a User but only with username and email
	 * $User = new User();
	 * $User->fromArray($_POST, array('username', 'email'));
	 * </code>
	 * 
	 * @todo check if this works with [Model][id] notation in $fieldNames
	 * @param array(mixed) $data
	 * @param array(string) name of the field that should be set (so you can ignore keys)
	 * @return Model
	 */
	public function fromArray(Array $data = array(), Array $fieldNames = array()) 
	{
		foreach($data as $key => $value) {
			if (count($fieldNames) > 0 && !in_array($key, $fieldNames)) continue;
			$this->set($key, $value);
		}
		return $this;
	}
	
	/**
	 * Fills Model data by searching the database table for a matching primaryKey
	 * value.
	 * @param integer $id
	 * @return boolean
	 */
	public function fromId($id) 
	{
		if (!$model = $this->findBy($this->primaryKeyName, $id)) {
			return false;
		}
		$this->set($this->primaryKeyName, (int) $id);
		$this->data = $model->toArray();
		foreach($this->belongsTo + $this->hasOne as $modelName => $config) {
			$this->$modelName = $model->$modelName;
		}
		foreach($this->hasMany + $this->hasAndBelongsToMany as $modelName => $config) {
			$modelPlural = Inflector::plural($modelName);
			$this->{$modelPlural} = $model->$modelPlural;
			$this->{$modelName} = $model->$modelName;
		}
		return true;
	}
	
	/**
	 * Returns the model data as array or just the fields from the model
	 * that you’ve named in $fieldNames or except the fields named in $except.
	 * 
	 * @param array(string) $fieldNames Field names to include
	 * @param array(string) $except Field names to ignore
	 * @return array(mixed)
	 */
	public function toArray($fieldNames = null, $except = null) 
	{
		if (func_num_args() == 0) {
			return $this->data;
		}
		if ($fieldNames === null) {
			$fieldNames = array_keys($this->structure);
		}
		$data = array();
		foreach((array) $fieldNames as $fieldname) {
			if ((is_array($except) && in_array($fieldname, $except)) || $except == $fieldname) {
				continue;
			}
			$data[$fieldname] = $this->get($fieldname);
		}
		return $data;
	}
	
	/**
	 * Update a single field of a model
	 * 
	 * Save a new value for one field of a model and save it emediently.
	 * Returns true if everything worked fine, or false if the model did not
	 * exists (i.e. the id was not set) or fieldname not found in this model.
	 * 
	 * This can be used to fast set publication status for example
	 * <code>
	 * $entry->saveField('public', true);
	 * </code>
	 * 
	 * @param string $fieldname
	 * @param mixed $value
	 * @return boolean
	 */
	public function saveField($fieldname, $value) 
	{
		if (!$this->exists() || !$this->hasField($fieldname)) return false;
		$this->query('UPDATE '.$this->tablename.' SET `'.$fieldname.'` = '.DBQuery::quote($value, $this->structure[$fieldname]->quoting).' WHERE '.$this->primaryKeyName.' = '.$this->get($this->primaryKeyName));
		$this->set($fieldname, $value);
		return $this->save();
	}
	
	/**
	 * Save Model Data to database table
	 * 
	 * Also calls some callbacks depending on if this model entry allready
	 * exists (checked via {@link exists}): {@link beforeInsert}/{@link beforeUpdate},
	 * {@link beforeSave}, {@link afterInsert}/{@link afterUpdate}, {@link afterSave}
	 * and also calling these events on all attached behaviors.
	 * 
	 * @param boolean $validate	set to false to skip validation
	 * @return boolean
	 */
	public function save($validate = true) 
	{
		if (!($this->beforeSave($this) && $this->behaviors->call('beforeSave', array($this)))) {
			return false;
		}
		// validate model data first
		if ($validate && !$this->validate($this->data)) {
			return false;
		}
		// insert or update
		if (!$this->exists()) {
			$this->insert();
		} else {
			$this->update();
		}
		$this->afterSave();
		$this->behaviors->call('afterSave');;
		return $this;
	}
	
	/**
	 * Save Model Data and all associated Model data present.
	 * 
	 * @param boolean $validate 
	 * @return boolean
	 */
	public function saveAll($validate = true)
	{
		$saveResult = $this->save($validate);
		if ($saveResult) {
			// save hasOne
			foreach($this->hasOne as $modelName => $config) {
				if (isset($this->{$modelName})) {
					$this->{$modelName}->set($config['associationKey'], $this->get($this->primaryKeyName));
					$res = $this->{$modelName}->save();
				}
			}
			foreach($this->belongsTo as $modelName => $config) {
				if (isset($this->{$modelName})) {
					$res = $this->{$modelName}->save();
				}
			}
			// save has many
			foreach($this->hasMany as $modelName => $config) {
				$plural = Inflector::plural($modelName);
				if (!empty($this->{$plural})) foreach($this->{$plural} as $model) {
					$model->set($config['associationKey'], $this->get($this->primaryKeyName));
					$model->save();
				}
			}
		}
		// save HABTM associated models
		if (is_array($this->hasAndBelongsToMany) && $this->depth > 0) {
			foreach($this->hasAndBelongsToMany as $modelName => $config) {
				$pluralName = Inflector::plural($modelName);
				// remove previosly saved
				$this->query(new DeleteQuery($config['joinTable'].' '.$config['with'], array($config['foreignKey'] => $this->get($this->primaryKeyName))));
				// add new data
				foreach($this->{$pluralName} as $model) {
					if (!($model instanceof Model)) continue;
					$model->save();
					$values = array(
						$config['foreignKey'] => $this->get($this->primaryKeyName),
						Inflector::underscore($model->name, true).'_'.$model->primaryKeyName => $model->get($model->primaryKeyName)
					);
					// get join data from join table
					if (isset($model->data[$this->name.$modelName])) {
						$values = array_merge($values, $model->{$this->name.$modelName});
					}
					$q = new InsertQuery($config['joinTable'], $values);
					$q->verb = 'REPLACE';
					$this->query($q);
				}	
			}
		}
		return true;
	}
	
	/**
	 * Called before insert or update action takes places
	 * @return boolean
	 */
	public function beforeSave() 
	{
		// check if associate models defined
		foreach($this->belongsTo + $this->hasOne as $modelName => $config) {
			if (!isset($this->{$modelName})) {
				continue;
			}
			$model = $this->{$modelName};
			if ($model instanceof Model && !$model->isEmpty($this->{$modelName}->primaryKeyName)) {
				$this->set(Inflector::delimeterSeperate($modelName.'_'.$model->primaryKeyName, '_', true), $model->get($model->primaryKeyName));
			}
		}
		return true;
	}
	
	public function afterSave() 
	{
		return true;
	}
	
	/**
	 * Insert model data into model database table
	 * 
	 * @param array(string) $data
	 * @return boolean
	 */
	protected function insert()
	{
		if (!($this->beforeInsert() && $r = $this->behaviors->call('beforeInsert'))) {
			return false;
		}
		$quotedData = array();
		foreach($this->structure as $key => $value) {
			// if key not set
			if (!isset($this->data[$key])) {
				continue;
			}
			// empty primary key value not use
			if ($key == $this->primaryKeyName && $this->isEmpty($key)) {
				$quotedData[$key] = 'NULL';
			} else {
				$quotedData[$key] = DBQuery::quote($this->data[$key], $this->structure[$key]->quoting);
			}
		}
		$q = new InsertQuery($this->tablename, $quotedData);		
		$db = DBConnectionManager::getInstance()->get($this->useDBConfig);
		$db->query($q);
		$this->set($this->primaryKeyName, $db->lastInsertId());
		$this->afterInsert();
		$this->behaviors->call('afterInsert');
		return true;
	}
	
	public function beforeInsert() 
	{
		return true;
	}
	
	public function afterInsert() 
	{
		return true;
	}
	
	/**
	 * 
	 * @param array(string) $data
	 * @return unknown
	 */
	protected function update()
	{
		if (!($this->beforeUpdate() && $this->behaviors->call('beforeUpdate'))) return false;
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
	
	/**
	 * Deletes model entries matching the $conditions
	 * @param $conditions
	 * @return Model|boolean
	 */
	public function updateWhere($conditions = array(), $values = array()) 
	{
		$updateQuery = new UpdateQuery($this->tablename(), $values, $conditions);
		$this->query($updateQuery);
		return $this;
	}
	
	public function beforeUpdate() 
	{
		// set created date if there's any
		if (!$this->exists()) {
			throw new ModelEmptyPrimaryKeyException($this);
		}
		return true;
	}
	
	public function afterUpdate() 
	{
		return true;
	}
	
	/**
	 * Delete model from database table
	 * 
	 * <code>
	 * $User = new User(23);
	 * $User->delete();
	 * </code>
	 * 
	 * @param integer|Model $id
	 * @return boolean
	 */
	public function delete($id = null) 
	{
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
		$db = DBConnectionManager::getInstance()->get($this->useDBConfig);
		$db->query(new DeleteQuery($this->tablename, array($this->primaryKeyName => $id)));
		$this->afterDelete();
		$this->behaviors->call('afterDelete');
		$this->reset();
		return true;
	}
	
	/**
	 * Deletes model entries matching the $conditions
	 * @param $conditions
	 * @return Model|boolean
	 */
	public function deleteWhere($conditions, $callbacks = false) 
	{
		if ($callbacks) {
			
		} else {
			$deleteQuery = new DeleteQuery($this->tablename(), $conditions);
			$this->query($deleteQuery);
		}
		return $this;
	}
	
	/**
	 * Callback called before {@link delete} starts deleting, this should
	 * return false to stop the deleting process.
	 * @param integer $id
	 * @return true
	 */
	protected function beforeDelete($id)
	{
		return true;
	}
	
	/**
	 * Called after the model data was successfully deleted, the return value
	 * of this is not so important.
	 * @return boolean.
	 */
	protected function afterDelete()
	{
		foreach($this->hasOne as $name => $config) {
			if (!$config['dependent'] || empty($this->{$name})) continue;
			$this->{$name}->delete();
		}
		// delete hasMany stuff
		if ($this->hasMany) {
			foreach($this->hasMany as $name => $config) {
				$plural = Inflector::plural($name);
				if (!$config['dependent'] || empty($this->{$plural})) continue;
				foreach($this->{$plural} as $model) {
					$model->delete();
				}
			}
		}
		// delete hasAndBelongsToMany associated 
		if (is_array($this->hasAndBelongsToMany)) {
			foreach($this->hasAndBelongsToMany as $modelName => $config) {
				$pluralName = Inflector::plural($modelName);
				if (!$config['dependent'] || empty($this->{$plural})) continue;
				foreach($this->{$pluralName} as $model) {
					$model->delete();
				}
				$this->{$pluralName}->q('DELETE * FROM '.$config['joinTable'].' WHERE '.$config['foreignKey'].' = '.$this->get($this->primaryKeyName));
			}
		}
		return $this;
	}
	
	/**
	 * Returns true if the model is saved in the database table.
	 * 
	 * If you call this method with a primary key value it will search for an
	 * entry in the database with this primary key.
	 * <code>
	 * if ($blogEntry = $this->BlogEntry->exists($postedId)) {
	 * 	echo 'blog entry found');
	 * } else {
	 * 	echo 'sorry there\'s no blogEntry with that id';
	 * }
	 * </code>
	 * 
	 * @param integer $id
	 * @return boolean|Model
	 */
	public function exists($id = null) 
	{
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
	 * Validates the models data or the passed data using the {@link validate}
	 * array and returns the result as boolean. The errors occured during
	 * validation are stored in {@link validationErrors}.
	 * 
	 * Code from a controller action
	 * <code>
	 * if(!$this->User->validate($this->request->data)) {
	 * 	$this->set('errorMessages', $this->User->validationErrors);
	 * 	$this->set('success', false);
	 * } else {
	 * 	$this->User->fromArray($this->request->data));
	 * 	$this->User->save();
	 * 	$this->set('success', true);
	 * }
	 * </code>
	 *
	 * @param array(mixed) $data
	 * @param array(string) $fieldNames
	 * @return boolean
	 */
	public function validate($data = array(), $fieldNames = array()) 
	{
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
	 * Validates $value to the rules assigned to $fieldName
	 * 
	 * @param string $fieldName
	 * @param mixed $value
	 * @return boolean|string
	 */
	public function validateField($fieldName, $value) 
	{
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
	 * Creates a default select query including all associated models defined
	 * in {@link belongsTo}, {@link hasMany}, {@hasOne} ...
	 * 
	 * @param integer $depth depth of model associations to use in select query
	 * @return SelectQuery
	 */
	public function createSelectQuery($conditions = array(), $order = null, $offset = 0, $count = null, $depth = null) 
	{
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
		$order = array_merge((array) $order, $this->order);
		// add this models
		if (count($order) > 0) {
			foreach($order as $fieldname => $direction) {
				if (is_numeric($fieldname)) {
					if ($spacePos = strrpos($direction, ' ')) {
						$fieldname = substr($direction, 0, $spacePos);
						$direction = trim(substr($direction, $spacePos));
					} else {
						$fieldname = $direction;
						$direction = null;
					}
				}
				// prepend thi smodel name if missing in fieldname
				if (!strpos($fieldname, '.') && $this->hasField($fieldname)) {
					$fieldname = $this->name.'.'.$fieldname;
				}
				$query->orderBy($fieldname, $direction);
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
		if ($depth >= 0) {
			foreach($this->belongsTo as $modelAlias => $config) {
				$this->belongsTo[$modelAlias]['associationKey'] = $this->name.strrchr($config['associationKey'], '.');
			}
			foreach($this->hasOne + $this->belongsTo as $modelName => $config) {
				foreach($this->{$modelName}->structure as $fieldInfo) {
					$query->select($modelName.'.'.$fieldInfo->name, $modelName.'.'.$fieldInfo->name);
				}
				$joinConditions = $config['conditions'];
				$joinConditions[$config['associationKey']] = $config['foreignKey'];
				$query->join($this->{$modelName}->tablename, ucFirst($modelName), DBQuery::JOIN_LEFT, $joinConditions);
			}
			// HABTM
			$tmpR = $query->render();
			foreach($this->hasAndBelongsToMany as $modelName => $config) {
				if (!preg_match('@'.$modelName.'\.@i', $tmpR)) continue;
				$query->groupBy($this->name.'.'.$this->primaryKeyName);
				// die(var_dump($config));
				$query->join($config['joinTable'], null, DBQuery::JOIN_LEFT, array(
					$config['foreignKey'] => $config['associationKey']
				));
				$query->join($this->{$modelName}->tablename, ucFirst($modelName), DBQuery::JOIN_LEFT, array(
					$config['joinTable'].'.'.Inflector::underscore($modelName).'_'.$this->{$modelName}->primaryKeyName => $modelName.'.'.$this->{$modelName}->primaryKeyName
				));
			}
		}
		return $query;
	}
	
	/**
	 * Turns a database result into a list of models and returns it
	 * 
	 * @param QueryResult $result
	 * @param boolean $justOne
	 * @param integer	$depth	Depth of model association depth
	 * @return IndexedArray
	 */
	public function createSelectResultList(QueryResult $result, $justOne = false, $depth = null) 
	{
		if ($depth === null) {
			$depth = $this->depth;
		}
		if ($result->numRows() == 0) {
			return false;
		}
		$return = new ObjectSet(get_class($this));
		$classname = get_class($this);
		while($modelData = $result->fetchAssoc()) {
			$modelClassName = $classname;
			// use model name from DB Result if set
			if (!empty($modelData['use_model'])) {
				$modelClassName = ucFirst($modelData['use_model']);
			} elseif (!empty($modelData[$this->name.'.use_model'])) {
				$modelClassName = ucFirst($modelData[$this->name.'.use_model']);
			}
			if (!class_exists($modelClassName)) {
				ephFrame::loadClass('app.lib.model.'.$modelClassName);
			}
			$model = new $modelClassName($modelData, $this->name);
			$model->findConditions = $this->findConditions;
			$model->{$this->name} = $this;
			// hasOne, belongsTo data
			foreach($this->belongsTo + $this->hasOne as $modelName => $config) {
				$modelClassname2 = coalesce(@$config['class'], $modelName);
				if (!isset($model->{$modelName})) {
					$model->{$modelName} = new $modelClassname2(false, false);
					$model->{$modelName}->name = $modelName;
					$model->{$modelName}->fromArray($modelData);
					$model->{$modelName}->depth = $depth - 1;
				}
			}
			// fetch associated data if detph is larger than one
			if ($depth >= 1) {
				// fetch hasMany associated Models
				foreach($this->hasMany as $modelName => $config) {
					$primaryKeyValue = $model->get($model->primaryKeyName);
					if (empty($primaryKeyValue)) continue;
					$associatedModelNamePlural = Inflector::plural($modelName);
					$joinConditions = array_merge($config['conditions'], array(
						$config['associationKey'] => $primaryKeyValue
					));
					if ($this->{$modelName} instanceof Model) {
						$associatedData = $this->{$modelName}->findAll($joinConditions, null, 0, $config['count'], $depth - 1);
					}
					if (empty($associatedData)) {
						$associatedData = new ObjectSet($config['class']);
					}
					$model->{$associatedModelNamePlural}->{$this->name} = $this;
					$model->{$associatedModelNamePlural} = $associatedData;
				}
				// fetch HABTM associated models
				foreach($this->hasAndBelongsToMany as $modelName => $config) {
					// include habtm model if primary key not empty
					if ($model->isEmpty($model->primaryKeyName)) continue;
					// add maybe missing tableprefix @todo clean this tableprefix usage everywhere
				 	$conditions = array_merge($config['conditions'], array(
						$config['foreignKey'] => $model->get($model->primaryKeyName),
						$config['with'].'.'.$modelName.'_'.$this->{$modelName}->primaryKeyName => $modelName.'.id'
					));
					$query = $this->{$modelName}->createSelectQuery(null, $config['order']);
					$query->select->prepend($this->name.$modelName.'.*');
					$query->join($config['joinTable'], $this->name.$modelName, DBQuery::JOIN, $conditions);
					$query->orderBy->add($config['order']);
					$query->count($config['count']);
					$modelNamePlural = Inflector::plural($modelName);
					$model->{$modelNamePlural} = new ObjectSet($modelName);
					if ($r = $this->{$modelName}->query($query)) {
						$model->{$modelNamePlural} = $r;
					}
				}
			}
			$return->add($model);
			if ($justOne) break;
		}
		if ($justOne) {
			$return = $return[0];
		}
		return $return;
	}
	
	/**
	 * Send a query to the db and return result
	 * @param $query
	 * @param $depth
	 * @return Boolean|Model|Set
	 */
	public function query($query, $depth = null) 
	{
		if ($db = DBConnectionManager::getInstance()->get($this->useDBConfig)) {
			$result = $db->query($query);
			return $this->createSelectResultList($result, false, $depth);
		}
		return $r;
	}
	
	/**
	 * Search a single Row and return it as Model
	 * 
	 * <code>
	 * // search a single user by his mail addy
	 * $User->find(array('email' => 'love@ephigenia'));
	 * // search a single blog entry by it's primary key value
	 * $Comment->find(666);
	 * </code>
	 *
	 * @param string|array $conditions
	 * @param array $order
	 * @return Model|boolean
	 */
	public function find($conditions = array(), $order = null, $depth = null) 
	{
		$query = $this->createSelectQuery($conditions, $order, null, null, $depth);
		if (!$this->beforeFind($query)) return false;
		if ($r = $this->query($query, $depth)) {
			return $this->afterFind($r[0]);
		}
		return false;
	}
	
	/**
	 * Finds one entry from the model with the matching conditions and order rules
	 * @param array(string)|string $conditions
	 * @param array(string)|string $order
	 * @param integer	$depth
	 * @return Model|boolean
	 */
	public function findOne($conditions, $order = null, $depth = null) 
	{
		if ($ret = $this->findAll($conditions, $order, 0, 1, $depth)) {
			return $ret[0];
		}
		return false;
	}
	
	/**
	 * Callback get's called before {@link find} query is send to database
	 * @param string|Query $query
	 * @return boolean|Query
	 */
	public function beforeFind($query) 
	{
		return $query;
	}
	
	/**
	 * Callback called before $results are returned from the model
	 * @param mixed $results
	 * @return mixed
	 */
	public function afterFind($results) 
	{
		return $results;
	}
	
	/**
	 * Tries to return a Model from the table that matches the $key => $value
	 * rule passed to this method. These method handles all the
	 * $model->getby[fieldnam] calls.<br />
	 * <br />
	 * If the model has a field named like $fieldname the $value string is
	 * automatically quoted with the fields appropriate quoting type. So you
	 * don’t need to quote user names or passwords on user logins to prevent
	 * SQL-Injections when using {@link findyBy}
	 * 
	 * <code>
	 * // find user by email addy
	 * $User->findBy('email', 'love@ephigenia.de');
	 * // find by primary id (just one parameter)
	 * $User->findBy(666);
	 * // find first entry where lastlogin = NULL (not filled)
	 * $User->findBy('lastlogin', null);
	 * </code>
	 * 
	 * @param string 	$fieldname
	 * @param string 	$value
	 * @param integer $depth
	 * @return Model|boolean
	 */
	public function findBy($fieldname, $value = null, $depth = null) 
	{
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
	 * Returns an array of $id => $fieldname values from this model, you can
	 * use this method for creating simple dropdown lists.
	 * <code>
	 * // fill a dropdown with all users found
	 * $dropDown = new FormField('user_id');
	 * $dropDown->addOptions($User->listAll('User.name', null, array('name DESC'));
	 * </code>
	 * 
	 * You can also use 'templates' for the list like this:
	 * <code>
	 * // fill a dropdown with all users found
	 * $dropDown = new FormField('user_id');
	 * $dropDown->addOptions($User->listAll('%User.name% (%User.email%)', null, array('name DESC'));
	 * </code>
	 *
	 * @param $conditions
	 * @return array(string)
	 */
	public function listAll($fieldname, $conditions = array(), $order = array(), $offset = 0, $count = null, $depth = 0) 
	{
		$list = array();
		if (!($r = $this->query($this->createSelectQuery($conditions, $order, $offset, $count, $depth), $depth))) {
			return $list;
		}
		if (is_array($fieldname)) {
			$fieldname = ArrayHelper::implodef($fieldname, ' ', ':%2$s');
		}
		foreach ($r as $obj) {
			if (strpos($fieldname, ':') !== false) {
				$arr = $obj->toArray();
				foreach($obj->belongsTo + $obj->hasOne + $obj->hasMany as $modelName => $config) foreach($obj->{$modelName}->toArray() as $k => $v) {
					$arr[$modelName.'.'.$k] = $v;
				}
				$entry = String::substitute($fieldname, $arr);
			} else {
				$entry = $obj->get($fieldname);
			}
			$list[$obj->id] = $entry;
		}
		return $list;
	}
	
	/**
	 * Returns a {@link IndexedArray} of $conditions matching Models ordererd by $order.
	 *
	 * <code>
	 * // find all comments from one user
	 * $Comment->findAll(array('user_id' => 23));
	 * // find all entries, but 10 of them, starting at offset 5
	 * $Comment->findAll(null, null, 10, 5);
	 * // find all entries, ordered by comments_count and creation time
	 * $BlogEntry->findAll(null, array('creation DESC', 'comments_count DESC');
	 * </code>
	 * 
	 * @param array(string)|string $conditions
	 * @param array(string)|string $order
	 * @param integer $count Number of model items to return
	 * @param integer $offset Offset to select from
	 * @return IndexedArray(Model)|boolean
	 */
	public function findAll($conditions = null, $order = null, $offset = 0, $count = null, $depth = null) 
	{
		if (!($query = $this->beforeFind($this->createSelectQuery($conditions, $order, $offset, $count, $depth)))) return false;
		if ($r = $this->query($query)) {
			return $this->afterFind($r);
		}
		return false;
	}
	
	/**
	 * Returns true if something from this model is found that meets
	 * the $conditions you passed, otherwise false
	 * @param array(string) $conditions
	 * @return boolean
	 */
	public function hasAny($conditions) 
	{
		return ($this->findOne($conditions) !== false);
	}
	
	/**
	 * Returns random amount of entries from the model
	 * @param array(string) $conditions
	 * @return IndexedArray(Model)|boolean
	 */
	public function findAllRandom($conditions = null, $count = 0, $depth = null) 
	{
		return $this->findAll($conditions, array('RAND()'), 0, $count, $depth);
	}
	
	/**
	 * Returns a single random row from the model
	 * @param array(string) $conditions
	 * @return Model|boolean
	 */
	public function findRandom($conditions = null) 
	{
		return $this->find($conditions, array('RAND()'));
	}
	
	/**
	 * Returns the number of entries found
	 * @param array(string) $conditions
	 * @param integer $offset
	 * @param integer $count
	 * @return integer
	 */
	public function countAll($conditions = null, $offset = null, $count = null) 
	{
		$query = $this->createSelectQuery($conditions, array(), $offset, $count, null, null);
		$query->select = new Hash(array('COUNT(*)' => 'count'));
		if (!($result = $this->query($query))) {
			return false;
		}
		return (int) $result[0]->get('count');
	}
	
	/**
	 * Returns an array with information about pagination in this model
	 * @todo maybe this is not part of model, maybe it's more controller-like?
	 * @param integer $page Current Page Number
	 * @param integer $perPage number of items per page
	 * @param array(string) $conditions Conditions to mention when paginating
	 * @return array()
	 */
	public function paginate($page = 1, $perPage = null, $conditions = null) 
	{
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
	 * Returns all entries where $fieldname = $value. The values should be
	 * quoted with DBQuery::quote();
	 * 
	 * <code>
	 * // find all users whose usernames start with an a
	 * $User->findAllBy('SUBSTR(username, 0, 1)', 'a');
	 * // find all users that never logged on
	 * $User->findAllBy('lastlogin', NULL, false);
	 * // find all users that never logged in sort by creation date
	 * $User->findAllBy('lastlogin', NULL
	 * </code>
	 * 
	 * @todo implement that $value can be an array and the query uses $fieldname in (item, item) ...
	 * @param mixed $fieldname
	 * @param mixed $value
	 * @param array(string) $order
	 * @param integer $offset
	 * @param integer $count
	 * @param integer $depth depth of model recursion (0-2);
	 * @return IndexedArray(Model)|boolean
	 */
	public function findAllBy($fieldname, $value = null, $order = null, $offset = 0, $count = null, $depth = null) 
	{
		if ($this->hasField($fieldname)) {
			$value = DBQuery::quote($value, $this->structure[$fieldname]->quoting);
			if (strchr($fieldname, '.') == false) {
				$fieldname = $this->name.'.'.$fieldname;
			}
		} else {
			$value = DBQuery::quote($value);
		}
		$conditions = array($fieldname => $value);
		return $this->findAll($conditions, $order, $offset, $count, $depth);
	}
	
	/**
	 * Method handling find[By|All|AllBy][fieldName] and
	 * $model->username() returns 'username' index of data if 'username' is a
	 * part of the table structure.
	 * 
	 * @param string $methodName
	 * @param array $args
	 */
	public function __call($methodName, Array $args) 
	{
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
	 * Tests if a field exists in model structure.
	 * 
	 * This tests if a specific field is defined for this model. This will
	 * work with model and without model in front of fieldname:
	 * <code>
	 * // both the same
	 * $user->hasField('username');
	 * $user->hasField('User.username');
	 * // but this will fail:
	 * $user->hasField('Something.username');
	 * </code>
	 * 
	 * @param string $fieldname
	 * @return boolean
	 */
	public function hasField($fieldname) 
	{
		if (empty($fieldname)) return false;
		list($modelname, $fieldname) = Inflector::splitModelAndFieldName($fieldname);
		if (!empty($modelname) && $modelname !== $this->name) {
			return false;
		}
		return isset($this->structure[$fieldname]);
	}
	
	/**
	 * Tests if $fieldname field is not empty.
	 *
	 * @param string	$fieldname
	 * @param mixed	$default
	 * @return boolean
	 */
	public function isEmpty($fieldname) 
	{
		$r = $this->get($fieldname);
		return empty($r);
	}
	
	/**
	 * Returns the value of a model field or a default value if the field was
	 * empty.
	 *
	 * <code>
	 * echo 'URL: '.$TextEntry->get('url', 'no url given').'<br />';
	 * </code>
	 *
	 * You can also integrate this into boolean constructs
	 * <code>
	 * if ($url = $TextEntry->get('url', false)) {
	 * echo $url;
	 * } else {
	 * echo 'no url given';
	 * }
	 * </code>
	 * 
	 * @param string	$fieldname
	 * @param mixed	$default
	 * @return mixed
	 */
	public function get($fieldname, $default = null) 
	{
		if (is_scalar($fieldname)) {
			if (substr($fieldname, 0, strlen($this->name)) == $this->name) {
				$fieldname = substr($fieldname, strlen($this->name) + 1);
			}
			if (empty($this->data[$fieldname]) && func_num_args() > 1) {
				return $default;
			} elseif (array_key_exists($fieldname, $this->data)) {
				return $this->data[$fieldname];
			} elseif (array_key_exists($fieldname, $this->structure)) {
				return null;
			} elseif ($pointPos = strpos($fieldname, '.')) {
				return $this->{substr($fieldname, 0, $pointPos)}->get(substr($fieldname, $pointPos+1));
			}
		}
		trigger_error(get_class($this).'->'.$fieldname.' undefined variable name', E_USER_ERROR);
	}
	
	public function __get($fieldname) 
	{
		return $this->get($fieldname);	
	}
	
	/**
	 * Set a model field value
	 * 
	 * Setting a new username and email and save model
	 * <code>
	 * $user->set('username', 'Marcel Eichner');
	 * $user->set('email', 'love@ephigenia.de');
	 * $user->save();
	 * </code>
	 * 
	 * Setting values of models associated with the model
	 * <code>
	 * $user->set('Company.status', false);
	 * $user->Company->set('status', false');
	 * $user->Company->save();
	 * </code>
	 *
	 * @param string $fieldname
	 * @param mixed $value
	 * @return Model
	 */
	public function set($fieldname, $value = null) 
	{
		list($modelname, $fieldname) = Inflector::splitModelAndFieldName($fieldname);
		if (!empty($modelname) && $modelname !== $this->name) {
			if (isset($this->$modelname) && $this->$modelname instanceof Model) {
				return $this->$modelname->set($fieldname, $value);
			}
		} elseif (isset($this->structure[$fieldname])) {
			// use quoting type of structure
			switch($this->structure[$fieldname]->quoting) {
				case ModelFieldInfo::QUOTE_BOOLEAN:
					$this->data[$fieldname] = (bool) $value;
					break;
				case ModelFieldInfo::QUOTE_FLOAT:
					$this->data[$fieldname] = (float) $value;
					break;
				case ModelFieldInfo::QUOTE_INTEGER:
					$this->data[$fieldname] = (int) $value;
					break;
				case ModelFieldInfo::QUOTE_STRING:
				default:
					$this->data[$fieldname] = (string) $value;
					break;
			}
		} elseif (is_object($value)) {
			$this->$fieldname = $value;
		} else {
			$this->data[$fieldname] = $value;
		}
		return $this;
	}
	
	/**
	 * Magic method
	 * @param $fieldName
	 * @param $value
	 * @return unknown_type
	 */
	public function __set($fieldname, $value) 
	{
		return $this->set($fieldname, $value);
	}
	
	/**
	 * Reset the model data and recreate associated models
	 * @return boolean
	 */
	public function reset() 
	{
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
	 * Loads Models structure from Database or cached Structure file into
	 * {@link structure} of this model.
	 * @return Model
	 */
	protected function loadStructure()
	{
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
		unset($db);
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
	 * Returns an array of all fieldnames of this model
	 * @return array(string)
	 */
	public function fieldNames() 
	{
		$fieldNames = array();
		foreach($this->structure as $fieldInfo) {
			$fieldNames[] = $fieldInfo->name;
		}
		return $fieldNames;
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.exception
 */
class ModelException extends ObjectException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.exception
 */
class ModelInvalidAssociationTypeException extends ModelException 
{
	public function __construct(Model $model, $associationType) 
	{
		$messge = 'Invalid association type detected: ’'.$associationType.'’ in Model '.$model->name.' (class: '.get_class($model).')';
		parent::construct($message);
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.exception
 */
class ModelEmptyPrimaryKeyException extends ModelException 
{
	public function __construct(Model $model) 
	{
		$message = 'The primary key on '.$model->name.'.'.$model->primaryKeyName.' is empty and can not be uesed!';
		parent::__construct($message);
	}
}