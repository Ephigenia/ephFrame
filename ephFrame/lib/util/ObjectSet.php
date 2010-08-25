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

class_exists('IndexedArray') or require dirname(__FILE__).'/IndexedArray.php';

/**
 * IndexedArray of Objects
 * 
 * The {@link ObjectSet} works like the {@link IndexedArray} class but only accepting
 * instances of a specific class to be in it.
 * 
 * You can use this class for creating Sets of Model Objects or other classes.
 * 
 * <code>
 * $s = new ObjectSet('Set');
 * $c = new IndexedArray(1,2,3,4);
 * $s->add($c);
 * </code>
 * 
 * This is partly tested in {@link TestObjectSet}
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 19.07.2008
 */
class ObjectSet extends IndexedArray 
{
	/**
	 * Name of class this ObjectSet allows to store
	 * @var string
	 */
	public $classname = '';
	
	/**
	 * Constructs a new {@link ObjectSet}
	 *
	 * @param string $classname
	 * @param array $data
	 * @return ObjectSet
	 */
	public function __construct($classname, $data = null) 
	{
		$this->classname = $classname;
		if (func_num_args() > 2) {
			$args = func_get_args();
			$data = array_slice($args, 2);
		}
		return parent::__construct($data);
	}
	
	protected function copy($data = array())
	{
		$classname = get_class($this);
		if (func_num_args() >= 1) {
			return new $classname($this->classname, $data);
		}
		return new $classname($this->classname, $this->data);
	}
	
	/**
	 * Searches for a object with the $property = $value and returns it
	 * @param string $property
	 * @param mixed $value
	 * @return Object|false
	 */
	public function findByProperty($property, $value)
	{
		foreach($this->data as $object) {
			if ($object->{$property} == $value) return $object;
		}
		return false;
	}
	
	/**
	 * Implode all objects $property value with $glue
	 * @param string $glue
	 * @param string $property
	 * @return string
	 */
	public function implodeProperty($glue = ',', $property)
	{
		return implode($glue, $this->extractProperty($property));
	}
	
	public function extractProperty($property)
	{
		$r = array();
		foreach($this as $obj) $r[] = $obj->{$property};
		return $r;
	}
	
	/**
	 * Adds an other object to the ObjectSet
	 * @param object $object
	 * @return ObjectSet
	 */
	public function add($object) 
	{
		$this->canBeAdded($object);
		return parent::add($object);
	}
	
	/**
	 * Tests if $object can be added to this {@link ObjectSet}. If $object
	 * is no object or instance of {@link classname} an exception is thrown.
	 * @throws ObjectSetObjectExpectedException
	 * @throws ObjectSetInvalidClassException
	 * @param mixed $object
	 * @return boolean
	 */
	private function canBeAdded($object)
	{
		if (!is_object($object)) {
			throw new ObjectSetObjectExpectedException($this, $object);
		}
		if (!empty($this->classname) && !($object instanceof $this->classname)) {
			throw new ObjectSetInvalidClassException($this, $object);
		}
		return true;
	}
	
	/**
	 * Sets a specific index of the {@link ObjectSet} to a specific value.
	 * This is like the {@link setValue} of the the parent class {@link Set}
	 * but with checking for the correct class of the $value
	 * @param integer $index
	 * @param object $val
	 */
	public function setValue($index, $object) 
	{
		$this->canBeAdded($object);
		return parent::setValue($index, $object);
	}
	
	/**
	 * Add an other $object to the beginning of the {@link ObjectSet}
	 * @param object $val
	 */
	public function prepend($val) 
	{
		$this->canBeAdded($object);
		return parent::prepend($val);
	}
	
	public function offsetSet($index, $value)
	{
		$this->canBeAdded($value);
		if ($index === null) {
			$index = $this->count();
		}
		$this->data[$index] = $value;
		return $this;
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class ObjectSetException extends IndexedArrayException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class ObjectSetObjectExpectedException extends ObjectSetException 
{
	public function __construct(ObjectSet $objectSet, $var) 
	{
		$message = sprintf('%s expects objects not %s.',
			get_class($objectSet), gettype($var));
		parent::__construct($message);
	}
}

/**
 * Thrown if an invalid object instance is tried to add to {@link ObjectSet}
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class ObjectSetInvalidClassException extends ObjectSetException 
{
	public function __construct(ObjectSet $objectSet, Object $object) 
	{
		$message = sprintf('%s expects instances of \'%s\', you passed \'%s\' which is not accepted.',
			get_class($objectSet), $objectSet->classname, get_class($object)
		);
		parent::__construct($message);
	}
}