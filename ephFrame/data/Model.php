<?php

namespace ephFrame\data;

class Model extends \ArrayObject
{
	public $connection = 'default';
	
	public $tablename = '';
	
	public $primaryKey = 'id';
	
	public $conditions = array();
	
	public $hasOne = array();
	
	public $hasMany = array();
	
	public $hasAndBelongsToMany = array();
	
	public $belongsTo = array();
	
	public $behaviors = array();
	
	public $displayField;
	
	public function __construct(Array $data = array())
	{
		return parent::__construct($data, \ArrayObject::ARRAY_AS_PROPS);
	}
	
	public function exists()
	{
		return !empty($this[$this->primaryKey]);
	}
	
	public function find(Array $params = array())
	{
		$params += array(
			'limit' => 1,
			'count' => 1,
		);
		return $this->findAll($params);
	}
	
	public function findBy($column, $value, Array $params = array())
	{
		return $this->find($params + array(
			'conditions' => array(
				$column => $value,
			),
		));
	}
	
	public function findAll(Array $params = array())
	{
		return $this->query('SELECT * FROM '.$this->tablename);
	}
	
	public function findAllBy($column, $value, Array $params = array())
	{
		return $this->findAll($params + array(
			'conditions' => array(
				$column => $value,
			),
		));
	}
	
	public function query($query)
	{
		$result = Connections::get($this->connection)->query($query);
		$return = array();
		while($data = $result->fetch(\PDO::FETCH_ASSOC)) {
			$return[] = new self($data);
		}
		return $return;
	}
	
	public function update(Array $values, Array $conditions = array())
	{
		
	}
	
	public function delete(Array $conditions = array())
	{
		if (func_num_args() == 0 && $this->exists()) {
			$this->query('DELETE FROM '.$this->tablename.' WHERE '.$this->primaryKey.' = '.$this[$this->primaryKey]);
		} else {
			
		}
	}
	
	public function save()
	{
		
	}
	
	public function __call($method, Array $args = array())
	{
		if (strncasecmp($method, 'findallby', 9) == 0) {
			array_unshift($args, lcfirst(substr($method, 9)));
			return call_user_func_array(array($this, 'findAllBy'), $args);
		} elseif (strncasecmp($method, 'findby', 6) == 0) {
			array_unshift($args, lcfirst(substr($method, 6)));
			return call_user_func_array(array($this, 'findBy'), $args);
		}
	}
	
	public function __toString()
	{
		if (!$this->displayField) {
			return implode(' ', (array) $this);
		} else {
			return $this[$this->displayField];
		}
	}
}

class ModelException extends \Exception {}