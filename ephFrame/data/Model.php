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
	
	public function find(Array $params = array())
	{
		$params += array(
			'limit' => 1,
			'count' => 1,
		);
		return $this->findAll($params);
	}
	
	public function findAll(Array $params = array())
	{
		$connection = Connections::get($this->connection);
		var_dump($connection);
	}
	
	public function update(Array $values, Array $conditions = array())
	{
		
	}
	
	public function delete(Array $conditions = array())
	{
		
	}
	
	public function save()
	{
		
	}
	
	public function __call($method, Array $args = array())
	{
		
	}
	
	public function __toString()
	{
		return $this[$this->displayField];
	}
}

class ModelException extends \Exception {}