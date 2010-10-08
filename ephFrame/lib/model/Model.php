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

$___d = dirname(__FILE__);
class_exists('DBConnectionManager') or require $___d.'/DB/DBConnectionManager.php';
class_exists('Inflector') or require $___d.'/../util/Inflector.php';
class_exists('SelectQuery') or require $___d.'/DB/SelectQuery.php';
class_exists('InsertQuery') or require $___d.'/DB/InsertQuery.php';
class_exists('UpdateQuery') or require $___d.'/DB/UpdateQuery.php';
class_exists('DeleteQuery') or require $___d.'/DB/DeleteQuery.php';
class_exists('ModelFieldInfo') or require $___d.'/ModelFieldInfo.php';
class_exists('ModelStructureCache') or require $___d.'/ModelStructureCache.php';
class_exists('ModelBehaviorHandler') or require $___d.'/ModelBehaviorHandler.php';
class_exists('ObjectSet') or require $___d.'/../util/ObjectSet.php';
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
	 * Name of the field of the model that should be used when the model
	 * is casted a string
	 * @var string
	 */
	public $displayField = false;
	
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
	protected $data = array();
	
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
	 * Cache Queries from this model?
	 * @var boolean
	 */
	protected $cacheQueries = false;
	
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
	public $perPage = 25;
	
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
		// model alias
		if (is_string($fieldNames)) {
			$this->name = $fieldNames;
		}
		// get DB Instance if tablename is set
		if ($this->tablename !== false) {
			$this->DB = DBConnectionManager::instance()->get($this->useDBConfig);
			if (empty($this->tablenamePrefix)) {
				$this->tablenamePrefix = Registry::get('DB.tablenamePrefix');
				$this->tablename();
			}
			$this->loadStructure();
		}

		// merge associations of this model with associations from parent models
		foreach ($this->associationTypes as $associationKey) {
			$this->__mergeParentProperty($associationKey);
		}
		$this->__mergeParentProperty('behaviors');
		$this->__mergeParentProperty('uses');
		$this->uses = new Collection($this->uses);
		$this->initAssociations();
		
		$this->afterConstruct();
		
		// initialize model behaviors
		$this->behaviors = new ModelBehaviorHandler($this, $this->behaviors);
		$this->behaviors->afterConstruct($this);
		// load inital data from array data or primary id
		if (is_array($id)) {
			$this->fromArray($id, is_array($fieldNames) ? $fieldNames : array());
		} elseif (is_int($id) || is_string($id)) {
			!$this->fromId($id);
		}
		return $this;
	}
	
	/**
	 * callback called after model finished constructing and init
	 */
	protected function afterConstruct() 
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
	protected function initAssociations()
	{
		foreach($this->associationTypes as $associationType) {
			if (!is_array($this->{$associationType})) {
				$this->{$associationType} = array();
			}
			foreach($this->{$associationType} as $alias => $config) {
				unset($this->{$associationType}[$alias]);
				if (is_int($alias)) {
					$alias = $config;
				}
				if (is_string($config)) {
					$config = array(
						'class' => $config,
					);
				}
				$config = $this->normalizeBindConfig($alias, $config, $associationType);
				Library::load($config['class']);
				$this->uses[] = $alias;
				$this->{$associationType}[$alias] = $config;
			}
		}
		return true;
	}
	
	protected function normalizeBindConfig($alias, Array $config = array(), $associationType)
	{
		$defaults = array(
			'dependent' => false,
			'conditions' => array(),
			'foreignKey' => false,
			'associationKey' => false,
			'joinTable' => false,
			'class' => false,
			'with' => false,
			'order' => array(),
			'limit' => false,
		);
		if (empty($config['class'])) {
			$config['class'] = $alias;
		}
		if (!isset($this->{Inflector::pluralize($alias)}) && in_array($associationType, array('hasMany', 'hasAndBelongsToMany'))) {
			$this->{Inflector::pluralize($alias)} = new IndexedArray();
		}
		$config = array_merge($defaults, $config);
		if ($associationType == 'hasAndBelongsToMany') {
			if (empty($config['with'])) {
				$config['with'] = $this->name.$alias;
			}
			if (empty($config['joinTable'])) {
				$config['joinTable'] = $this->tablename.'_'.Inflector::underscore(Inflector::pluralize($alias));
			}
			$config['joinTable'] = String::prepend($config['joinTable'], $this->tablenamePrefix, true);
			if (empty($config['foreignKey'])) {
				$config['foreignKey'] = $config['with'].'.'.Inflector::underscore($alias.'_'.$this->primaryKeyName);
			}
			if (empty($config['associationKey'])) {
				$config['associationKey'] = $config['with'].'.'.Inflector::underscore($this->name.'_'.$this->primaryKeyName);
			} elseif (strrpos($config['associationKey'], '.') == false) {
				$config['associationKey'] = $config['with'].'.'.$config['associationKey'];
			}
		}
		if (empty($config['foreignKey'])) switch ($associationType) {
			case 'belongsTo':
				$config['foreignKey'] = $alias.'.id';
				break;
			case 'hasOne':
				$config['foreignKey'] = $alias.'.'.Inflector::underscore($this->name).'_id';
				break;
			case 'hasMany':
				$config['foreignKey'] = $alias.'.'.$this->primaryKeyName;
				break;
		} elseif (strpos($config['foreignKey'], '.') === false && $associationType !== 'hasAndBelongsToMany') {
			$config['foreignKey'] = $alias.'.'.$config['foreignKey'];
		}
		if (empty($config['associationKey'])) switch ($associationType) {
			case 'belongsTo':
				$config['associationKey'] = $this->name.'.'.Inflector::underscore($alias).'_id';
				break;
			case 'hasOne':
				$config['associationKey'] = $this->name.'.'.$this->primaryKeyName;
				break;
			case 'hasMany':
				$config['associationKey'] = $alias.'.'.Inflector::underscore($this->name.'_'.$this->primaryKeyName);
				break;
		} elseif (strpos($config['associationKey'], '.') === false) {
			$config['associationKey'] = $this->name.'.'.$config['associationKey'];
		}
		if (strpos($config['class'], '.') === false) {
			$config['class'] = 'app.lib.model.'.$config['class'];
		}
		return $config;
	}
	
	/**
	 * Dynamicly Binds an other model to this model
	 * 
	 * This will remove previously made bindings.
	 *
	 * @param string $alias
	 * @param string $associationType
	 * @param array(string) $config
	 * @throws ModelInvalidAssociationTypeException
	 * @throws ModelReflexiveException if you try to bin the model to itsself
	 * @return boolean
	 */
	public function bind($alias, $associationType = null, Array $config = array(), $bind = false) 
	{
		if (!empty($associationType) && !$this->validAssociationType($associationType)) throw new ModelInvalidAssociationTypeException($this, $associationType);
		// prevent unlimited nesting
		if (is_object($bind)
			&& (
				get_class($bind) == $alias
				|| $bind->name == $alias
				|| isset($this->{$alias})
			)) {
			$this->{$alias} = $bind;
		} else {
			$config = $this->normalizeBindConfig($alias, $config, $associationType);
			$this->{$alias} = Library::create($config['class'], array($this, $alias));
		}
		$this->uses[] = $alias;
		$this->{$associationType}[$alias] = $config;
		$this->{$alias}->{$this->name} = $this;
		$this->{$alias}->{$this->name}->name = $this->name;
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
		$params = array_merge($params, array('id' => $this->id));
		$params = array_merge(array('controller' => $this->name, 'action' => 'view'), $params);
		if (!$uri = Router::getRoute('scaffold_actions', $params)) {
			$uri = WEBROOT.lcfirst($this->name).'/'.$this->id.'/';	
		}
		return $uri;
	}
	
	public function detailPageURL() 
	{
		return trim(Registry::get('WEBROOT_URL'), '/').$this->detailPageUri();
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
	public function fromArray(Array $data, Array $fieldNames = array()) 
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
		$this->set($this->primaryKeyName, $id);
		$this->data = $model->toArray();
		foreach($this->belongsTo + $this->hasOne as $modelName => $config) {
			$this->$modelName = $model->$modelName;
		}
		foreach($this->hasMany + $this->hasAndBelongsToMany as $modelName => $config) {
			$modelPlural = Inflector::pluralize($modelName);
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
			if (strstr($fieldname, '.') === false) {
				if (isset($this->data[$fieldname])) {
					$data[$fieldname] = $this->data[$fieldname];
				} else {
					$data[$fieldname] = false;
				}
			} else {
				$data[$fieldname] = $this->get($fieldname);
			}
		}
		return $data;
	}
	
	/**
	 * To String serializer.
	 * 
	 * Modify $displayField to get different results for every model
	 * @return string
	 */
	public function __toString()
	{
		if (empty($this->displayField)) {
			return parent::__toString();
		}
		if (is_array($this->displayField)) {
			$template = ':'.implode(' :', $this->displayField);
		} else {
			$template = $this->displayField;
		}
		if (strchr($template, ':')) {
			return String::substitute($this->displayField, $this->toArray());
		}
		return $this->get($this->displayField);
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
		return $this;
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
		if (!$this->beforeSave($this) || !$this->behaviors->beforeSave($this)) {
			return false;
		}
		if ($validate && !$this->validate($this->data)) {
			return false;
		}
		if ($this->exists()) {
			$this->update();
		} else {
			$this->insert();
		}
		$this->afterSave();
		$this->behaviors->afterSave($this, $validate);
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
				$plural = Inflector::pluralize($modelName);
				if (!empty($this->{$plural})) foreach($this->{$plural} as $model) {
					$model->set($config['associationKey'], $this->get($this->primaryKeyName));
					$model->save();
				}
			}
		}
		// save HABTM associated models
		if (is_array($this->hasAndBelongsToMany) && $this->depth > 0) {
			foreach($this->hasAndBelongsToMany as $modelName => $config) {
				$pluralName = Inflector::pluralize($modelName);
				// remove previosly saved
				$this->query(new DeleteQuery($config['joinTable'].' '.$config['with'], array($config['foreignKey'] => $this->get($this->primaryKeyName))));
				// add new data
				foreach($this->{$pluralName} as $model) {
					if (!($model instanceof Model)) continue;
					$model->save();
					$values = array(
						trim(substr($config['associationKey'], strrpos($config['associationKey'], '.')),'.') => $this->get($this->primaryKeyName),
						Inflector::underscore($model->name).'_'.$model->primaryKeyName => $model->get($model->primaryKeyName)
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
	protected function beforeSave() 
	{
		// update model keys
		foreach($this->belongsTo as $modelName => $config) {
			if (!isset($this->{$modelName}) || !($this->{$modelName} instanceof Model)) {
				continue;
			}
			$model = $this->{$modelName};
			if (!$model->isEmpty($model->primaryKeyName)) {
				$this->set($config['associationKey'], $model->get($model->primaryKeyName));
			}
		}
		return true;
	}
	
	protected function afterSave() 
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
		if (!$this->beforeInsert() || !$this->behaviors->beforeInsert($this)) {
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
		$db = DBConnectionManager::instance()->get($this->useDBConfig);
		$db->query($q, $this->cacheQueries);
		$this->set($this->primaryKeyName, $db->lastInsertId());
		$this->afterInsert();
		$this->behaviors->afterInsert($this);
		return true;
	}
	
	protected function beforeInsert() 
	{
		return true;
	}
	
	protected function afterInsert() 
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
		if (!$this->beforeUpdate() || !$this->behaviors->beforeUpdate($this)) return false;
		$quotedData = array();
		foreach($this->structure as $key => $value) {
			if (!isset($this->data[$key])) continue;
			$quotedData[$key] = DBQuery::quote($this->data[$key], $this->structure[$key]->quoting);
		}
		$q = new UpdateQuery($this->tablename, $quotedData, array($this->primaryKeyName => DBQuery::quote($this->data[$this->primaryKeyName], $this->structure[$this->primaryKeyName]->quoting)));
		$this->query($q);
		$this->afterUpdate();
		$this->behaviors->afterUpdate();
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
	
	protected function beforeUpdate() 
	{
		// set created date if there's any
		if (!$this->exists()) {
			throw new ModelEmptyPrimaryKeyException($this);
		}
		return true;
	}
	
	protected function afterUpdate() 
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
		if (!$this->beforeDelete($id) || !$this->behaviors->beforeDelete($this, $id)) return false;
		$db = DBConnectionManager::instance()->get($this->useDBConfig);
		$db->query(new DeleteQuery($this->tablename, array($this->primaryKeyName => $id)), $this->cacheQueries);
		$this->afterDelete();
		$this->behaviors->afterDelete($this);
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
				$plural = Inflector::pluralize($name);
				if (!$config['dependent'] || empty($this->{$plural})) continue;
				foreach($this->{$plural} as $model) {
					$model->delete();
				}
			}
		}
		// delete hasAndBelongsToMany associated 
		if (is_array($this->hasAndBelongsToMany)) {
			foreach($this->hasAndBelongsToMany as $modelName => $config) {
				$pluralName = Inflector::pluralize($modelName);
				if (!$config['dependent'] || empty($this->{$plural})) continue;
				foreach($this->{$pluralName} as $model) {
					$model->delete();
				}
				$conditions = array(
					trim(substr($config['associationKey'], strrpos($config['associationKey'], '.')),'.') => $this->get($this->primaryKeyName),
				);
				// @todo use conditions from config here
				$query = new DeleteQuery($config['joinTable'], array_merge($config['conditions'], $conditions));
				$this->{$pluralName}->q($query);
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
	public function validate($data = array(), Array $fieldNames = array()) 
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
	public function createSelectQuery(Array $params = array()) 
	{
		$defaults = array(
			'order' => array(),
			'offset' => 0,
			'limit' => 0,
			'conditions' => array(),
			'depth' => $this->depth,
			'group' => array(),
			'fields' => array_keys($this->structure),
		);
		$params = array_merge($defaults, $params);
		extract($params);

		$conditions = array_merge($this->findConditions, (array) $conditions);
		$query = new SelectQuery();
		$query->table($this->tablename, $this->name);
		$query->where->fromArray($conditions);
		if ($limit !== null) {
			$query->count((int) $limit);
		}
		if ($offset > 0) {
			$query->offset((int) $offset);
		}
		// add fields from this table
		foreach($fields as $name) {
			if (strpos($name, '.') === false) {
				$query->select($this->name.'.'.$name, $this->name.'.'.$name);
			} else {
				$query->select($name);
			}
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
		
		// Add join statements for assigned models
		if ($depth >= 0) {
			foreach($this->belongsTo as $modelAlias => $config) {
				$this->belongsTo[$modelAlias]['associationKey'] = $this->name.strrchr($config['associationKey'], '.');
			}
			foreach($this->hasOne + $this->belongsTo as $modelName => $config) {
				if ($this->uses->contains($modelName) === false) continue;
				foreach($this->{$modelName}->structure as $fieldInfo) {
					$query->select($modelName.'.'.$fieldInfo->name, $modelName.'.'.$fieldInfo->name);
				}
				$joinConditions = $config['conditions'];
				$joinConditions[$config['associationKey']] = $config['foreignKey'];
				$query->join($this->{$modelName}->tablename, ucFirst($modelName), DBQuery::JOIN_LEFT, $joinConditions);
				if (!empty($config['limit'])) {
					$query->groupBy($config['associationKey']);
				}
			}
			// HABTM
			$tmpR = $query->render();
			foreach($this->hasAndBelongsToMany as $modelName => $config) {
				if ($this->uses->contains($modelName) == false) continue;
				if (!preg_match('@'.$modelName.'\.@i', $tmpR)) continue;
				$query->groupBy($this->name.'.'.$this->primaryKeyName);
				$query->join($config['joinTable'], $config['with'], DBQuery::JOIN_LEFT, array(
					$this->name.'.'.$this->primaryKeyName => $config['associationKey']
				));
				$query->join($this->{$modelName}->tablename, ucFirst($modelName), DBQuery::JOIN_LEFT, array(
					$config['with'].'.'.Inflector::underscore($modelName).'_'.$this->{$modelName}->primaryKeyName => $modelName.'.'.$this->{$modelName}->primaryKeyName
				));
			}
		}
		if (!empty($group)) foreach((array) $group as $groupBy) {
			$query->groupBy($groupBy);
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
				Library::load('app.lib.model.'.$modelClassName);
			}
			$model = new $modelClassName($modelData, $this->name);
			$model->findConditions = $this->findConditions;
			$model->{$this->name} = $this;
			// hasOne, belongsTo data
			foreach($this->belongsTo + $this->hasOne as $modelName => $config) {
				if (!isset($model->{$modelName})) {
					$model->{$modelName} = Library::create(coalesce(@$config['class'], $modelName), array($modelData, $modelName));
					$model->{$modelName}->depth = $depth - 1;
				}
			}
			// fetch associated data if detph is larger than one
			if ($depth >= 1) {
				foreach($this->hasMany as $modelName => $config) {
					if (!$model->exists()) {
						continue;
					}
					$associatedModelNamePlural = Inflector::pluralize($modelName);
					if ($this->uses->contains($modelName) !== false) {
						$params = array(
							'conditions' => array_merge($config['conditions'], array(
								$config['associationKey'] => $model->get($model->primaryKeyName),
							)),
							'offset' => 0,
							'limit' => $config['limit'],
							'depth' => $depth - 1,
						);
						$associatedData = $this->{$modelName}->findAll($params);
					}
					if (empty($associatedData)) {
						$associatedData = new ObjectSet(ClassPath::classname($config['class']));
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
				 		$config['associationKey'] => $model->get($model->primaryKeyName),
				 		$config['with'].'.'.Inflector::underscore($modelName).'_id' => $modelName.'.id',
				 	));
					$query = $this->{$modelName}->createSelectQuery($config);
					$query->select->prepend($this->name.$modelName.'.*');
					$query->join($config['joinTable'], $this->name.$modelName, DBQuery::JOIN, $conditions);
					$query->orderBy($config['order']);
					$query->count($config['limit']);
					$modelNamePlural = Inflector::pluralize($modelName);
					if ($r = $this->{$modelName}->query($query)) {
						$model->{$modelNamePlural} = $r;
					} else {
						$model->{$modelNamePlural} = new ObjectSet($modelName);
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
		if ($db = DBConnectionManager::instance()->get($this->useDBConfig)) {
			$result = $db->query($query, $this->cacheQueries);
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
	 * @return Model|boolean
	 */
	public function find(Array $params = array()) 
	{
		$params['limit'] = 1;
		$query = $this->createSelectQuery($params);
		$query = $this->beforeFind($query);
		if (!$query) {
			return false;
		}
		if ($r = $this->query($query, @$params['depth'])) {
			$results = $this->afterFind($r);
			return $results[0];
		}
		return false;
	}
	
	/**
	 * Callback get's called before {@link find} query is send to database
	 * @param string|Query $query
	 * @return boolean|Query
	 */
	protected function beforeFind($query) 
	{
		return $query;
	}
	
	/**
	 * Callback called before $results are returned from the model
	 * @param mixed $results
	 * @return mixed
	 */
	protected function afterFind($results) 
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
	 * @param string $fieldname
	 * @param string $value
	 * @param array(string) additional query parameters
	 * @return Model|boolean
	 */
	public function findBy($fieldname, $value = null, Array $params = array()) 
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
		$defaults = array(
			'offset' => 0,
			'limit' => 1,
			'conditions' => array(
				$fieldname => $value
			),
			'depth' => $this->depth,
		);
		$params = array_merge($defaults, $params);
		return $this->find($params);
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
	 * @param string $fieldname
	 * @param array(string) $params
	 * @return array(string)
	 */
	public function listAll($fieldname = null, Array $params = array()) 
	{
		$list = array();
		if (empty($fieldname)) {
			$fieldname = $this->displayField;
		}
		$query = $this->createSelectQuery($params);
		if (!$query = $this->beforeFind($query)) {
			return $list;
		}
		if (!$result = $this->query($query, @$params['depth'])) {
			return $list;
		}
		if (is_array($fieldname)) {
			$fieldname = ArrayHelper::implodef($fieldname, ' ', ':%2$s');
		}
		foreach ($result as $obj) {
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
	 * @return IndexedArray(Model)|boolean
	 */
	public function findAll(Array $params = array()) 
	{
		$query = $this->createSelectQuery($params);
		$query = $this->beforeFind($query);
		if (!$query) {
			return false;
		}
		if ($result = $this->query($query, @$params['depth'])) {
			return $this->afterFind($result);
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
		return $this->findOne($conditions) !== false;
	}
	
	/**
	 * Returns random amount of entries from the model
	 * @param array(string) $conditions
	 * @return IndexedArray(Model)|boolean
	 */
	public function findAllRandom(Array $params = array()) 
	{
		$defaults = array(
			'order' => array('RAND()'),
		);
		return $this->findAll(array_merge($defaults, $params));
	}
	
	/**
	 * Returns a single random row from the model
	 * @param array(string) $conditions
	 * @return Model|boolean
	 */
	public function findRandom(Array $params = array())
	{
		$defaults = array(
			'limit' => 1,
		);
		$params = array_merge($params, $defaults);
		return $this->findAllRandom($params);
	}
	
	/**
	 * Returns the number of entries found
	 * @param array(string) $params
	 * @return integer
	 */
	public function countAll(Array $params = array()) 
	{
		$defaults = array(
			'depth' => 0,
			'field' => '*',
		);
		$params = array_merge($defaults, $params);
		$query = $this->createSelectQuery(array_merge($defaults, $params));
		// $query = $this->beforeFind($query);
		if (!empty($params['group']) && empty($params['field'])) {
			$params['field'] = 'DISTINCT('.implode(',', $params['group']).')';	
		}
		$query->select = new Hash(array('COUNT('.$params['field'].')' => 'count'));
		$query->groupBy = new Hash();
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
		$total = $this->countAll(array('conditions' => $conditions));
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
	 * @param string $fieldname
	 * @param string|integer $value
	 * @param array(string) Additional Query Paramaeters
	 * @return IndexedArray(Model)|boolean
	 */
	public function findAllBy($fieldname, $value = null, Array $params = array()) 
	{
		if (!is_array($value)) {
			if ($this->hasField($fieldname)) {
				$value = DBQuery::quote($value, $this->structure[$fieldname]->quoting);
				if (strchr($fieldname, '.') == false) {
					$fieldname = $this->name.'.'.$fieldname;
				}
			} else {
				$value = DBQuery::quote($value);
			}
		}
		$params['conditions'][$fieldname] = $value;
		return $this->findAll($params);
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
			if (isset($args[1])) {
				return $this->findAllBy(Inflector::underscore($found[3]), $args[0], $args[1]);
			} else {
				return $this->findAllBy(Inflector::underscore($found[3]), $args[0]);
			}
		// catch findBy[fieldname] calls 
		} elseif (preg_match('/find(By)?(.+)/i', $methodName, $found)) {
			array_unshift($args, Inflector::underscore($found[2]));
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
		if ($this->uses->count() > 0 && $this->uses->contains($fieldname) !== false) {
			foreach($this->associationTypes as $associationType) {
				foreach($this->{$associationType} as $alias => $config) {
					if ($alias == $fieldname) {
						$this->bind($fieldname, $associationType, $config);
						break 2;
					}
				}
			}
			if (!isset($this->{$fieldname}) && $this->uses->contains($fieldname) !== false) {
				$this->{$fieldname} = Library::create('app.lib.model.'.$fieldname);
			}
			return $this->{$fieldname};
		}
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
			if (isset($this->{$modelname}) && $this->{$modelname} instanceof Model) {
				return $this->{$modelname}->set($fieldname, $value);
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
			$this->{$fieldname} = $value;
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
			foreach($this->structure as $fieldName => $fieldInfo) {
				if (isset($this->data[$fieldName])) {
					continue;
				} elseif (isset($fieldInfo->default)) {
					$this->data[$fieldName] = $fieldInfo->default;
				} else {
					$this->data[$fieldName] = null;
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
		$this->reset();
		return $this;
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
		$message = 'Invalid association type detected: ’'.$associationType.'’ in Model '.$model->name.' (class: '.get_class($model).')';
		parent::__construct($model, $message);
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