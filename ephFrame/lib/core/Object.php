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

/**
 * Basic Object Class
 * 
 * Every object in the ephFrame and every object in the application should
 * be at least a child class ob Object. This is not to be meant a big mother
 * class or god-class - it's just for late time implementations or methods
 * that can be used in every child class.
 * 
 * All children inherit all functionality from this class so this class
 * should have as less methods as possible to avoid conflicts with the children's
 * methods.
 * 
 * One maxime of ephFrame framework is that every class should support chaining.
 * So for example the {@link DBSelectQuery} or {@link Image} class support it:
 * <code>
 * $obj->select('values')->where('1 = 1');
 * </code>
 * Please try to keep that in mind when you develop with ephFrame.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 06.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @version 0.201
 */
abstract class Object
{
	public $callbacks = array();
	
	/**
	 * Get (if second parameter is != null) or set a class variable
	 * on get action (second parameter is null) you can set a default
	 * value to be returned
	 * 
	 * @param string|object $varName
	 * @param mixed $varValue
	 * @param mixed $defaultReturn something that should be returned if get found nothin'
	 * @return Object|mixed
	 */
	public function __getOrSet($varName, $varValue = null, $defaultReturn = null)
	{
		// reading values
		if ($varValue === null) {
			if (isset($this->{$varName})) return $this->{$varName};
			if ($defaultReturn !== null) return $defaultReturn;
			throw new ObjectVariableNotFoundException();
		}
		// setting values
		$this->{$varName} = $varValue;
		return $this;
	}
	
	public function registerCallback($method, Array $callback)
	{
		$this->callbacks[$method][] = $callback;
		return $this;
	}
	
	public function callback($method, Array $arguments = array())
	{
		$result = true;
		if (!isset($this->callbacks[$method])) {
			return $result;
		}
		foreach($this->callbacks[$method] as $callback) {
			if (!($result = call_user_func_array(array($callback[0], $callback[1]), $arguments))) {
				return $result;
			}
		}
		return $result;
	}
	
	/**
	 * Calls a object's method
	 * @param string $name
	 * @param array(mixed) $args
	 * @return mixed
	 */
	public function callMethod($name, &$args = null)
	{
		return call_user_func_array(array(&$this, $name), $args);
	}
	
	/**
	 * Returns parent class names ordered by nesting level as array.
	 * 
	 * Simple example illustration how it works:
	 * <code>
	 * class A {};
	 * class B extends A {};
	 * class C extends B {};
	 * $c = new C();
	 * // should echo 'A,B'
	 * echo implode(',', $c->__parentClasses());
	 * </code>
	 * @param object $obj
	 * @return array(string)
	 */
	public function __parentClasses($obj = null)
	{
		global $parentClassesCache;
		if (func_num_args() == 0) {
			$obj = $this;
		}
		// create list of parent classes
		$className = get_class($obj);
		if (!isset($parentClassesCache[$className])) {
			$parentClassesCache[$className] = array();
			while($parentClass = get_parent_class($obj)) {
				$parentClassesCache[$className][] = $parentClass;
				$obj = $parentClass;
			}
		}
		return $parentClassesCache[$className];
	}
	
	/**
	 * Merge class properties with values from parent classes
	 * 
	 * @param string $varname name of parent classes
	 * @return Object
	 */
	public function __mergeParentProperty($varname, $cached = true)
	{
		global $mergeParentProperty;
		// do nothing if var not defined
		$className = get_class($this);
		if (!is_array($this->{$varname})) return $this;
		if (!isset($mergeParentProperty[$className.$varname]) || $cached == false) {
			foreach($this->__parentClasses($this) as $parentClassName) {
				$classVars = get_class_vars($parentClassName);
				// does parent class have a var named $name ?
				if (!isset($classVars[$varname]) || !is_array($classVars[$varname])) continue;
				$value = $classVars[$varname];
				// cycle through parents array values
				foreach($value as $index => $var) {
					if (in_array($var, $this->$varname)) continue;
					if (is_int($index)) {
						$this->{$varname}[] = $var;
					} elseif (!isset($this->{$varname}[$index])) {
						$this->{$varname}[$index] = $var;
					} elseif (is_array($this->{$varname}[$index]) && is_array($var)) {
						$this->{$varname}[$index] = array_merge($this->{$varname}[$index], $var);
					}
				}
			}
			$mergeParentProperty[$className.$varname] = $this->$varname;
		}
		return $mergeParentProperty[$className.$varname];
	}
	
	public function __toString()
	{
		return 'Object (class: '.get_class($this).')';
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ObjectException extends BasicException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ObjectMethodNotFoundException extends ObjectException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ObjectVariableNotFoundException extends ObjectException 
{}