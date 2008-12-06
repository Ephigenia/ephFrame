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

if (!class_exists('BasicException')) require_once dirname(__FILE__).'/lib/exception/BasicException.php';

/**
 *	Basic Object Class
 * 	
 * 	Every object in the ephFrame and every object in the application should
 * 	be at least a child class ob Object. This is not to be meant a big mother
 * 	class or god-class - it's just for late time implementations or methods
 * 	that can be used in every child class.
 * 
 * 	All children inherit all functionality from this class so this class
 * 	should have as less methods as possible to avoid conflicts with the children's
 * 	methods.
 * 
 * 	One maxime of ephFrame framework is that every class should support chaining.
 * 	So for example the {@link DBSelectQuery} or {@link Image} class support it:
 * 	<code>
 * 	$obj->select('values')->where('1 = 1');
 * 	</code>
 * 	Please try to keep that in mind when you develop with ephFrame.
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 06.05.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@version 0.201
 */
abstract class Object {
	
	/**
	 *	Get (if second parameter is != null) or set a class variable
	 * 	on get action (second parameter is null) you can set a default
	 * 	value to be returned
	 * 
	 * 	@param string|object $varName
	 * 	@param mixed $varValue
	 * 	@param mixed $defaultReturn something that should be returned if get found nothin'
	 * 	@return Object|mixed
	 */
	public function __getOrSet($varName, $varValue = null, $defaultReturn = null) {
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
	
	/**
	 *	Calls a object's method
	 * 	@param string $methodName
	 * 	@param array(mixed) $parameters
	 * 	@return mixed
	 */
	public function callMethod($methodName, &$parameters = null) {
		return call_user_func_array(array(&$this, $methodName), $parameters);
	}
	
	/**
	 *	Returns parent class names ordered by nesting level as array.
	 * 
	 * 	Simple example illustration how it works:
	 * 	<code>
	 * 	class A {};
	 * 	class B extends A {};
	 * 	class C extends B {};
	 * 	$c = new C();
	 * 	// should echo 'A,B'
	 * 	echo implode(',', $c->__parentClasses());
	 * 	</code>
	 * 	@param object $obj
	 * 	@return array(string)
	 */
	public function __parentClasses($obj = null) {
		$parents = array();
		if ($obj == null) {
			$obj = $this;
		}
		while($parentClass = get_parent_class($obj)) {
			$parents[] = $parentClass;
			$obj = $parentClass;
		}
		return $parents;
	}
	
	/**
	 *	merges an property array of the current class with all values from
	 * 	parents.
	 * 	@param string $name name of parent classes
	 */
	public function __mergeParentProperty($name) {
		foreach($this->__parentClasses() as $parentClassName) {
			$classVars = get_class_vars($parentClassName);
			if (!isset($classVars[$name])) continue;
			foreach($classVars[$name] as $index => $var) {
				if (!is_array($this->{$name}) || (is_array($this->{$name}) && in_array($var, $this->{$name}))) continue;
				if (is_string($index)) {
					$this->{$name}[$index] = $var; 
				} else {
					array_unshift($this->{$name}, $var);
				}
			}
		}
	}
	
	public function __toString() {
		return 'Object (class: '.get_class($this).')';
	}
	
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ObjectException extends BasicException {}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ObjectMethodNotFoundException extends ObjectException {}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ObjectVariableNotFoundException extends ObjectException {}

?>