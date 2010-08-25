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
 * Class for Handling Model Behaviors
 * 
 * This class handles the model behaviors for the model class.
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.model
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 16.10.2008
 */
class ModelBehaviorHandler extends Object implements Iterator, Countable 
{
	/**
	 * @var array(ModelBehavior)
	 */
	public $behaviors = array();
	
	/**
	 * @var Model
	 */
	public $model;
	
	public function __construct(Model $model, Array $behaviors = array()) 
	{
		$this->model = $model;
		$this->behaviors = $behaviors;
		$this->initBehaviors();
		return $this;
	}
	
	public function initBehaviors() 
	{
		foreach($this->behaviors as $behaviorName => $config) {
			unset($this->behaviors[$behaviorName]);
			// $behavior = array('Taggable', 'Deletable') notation
			if (is_int($behaviorName)) {
				$this->addBehavior($config, array());
			// $behavior = array('Taggable' => array('config')); notation
			} elseif (is_string($behaviorName) && is_array($config)) {
				$this->addBehavior($behaviorName, $config);
			}
		}
		return true;
	}
	
	public static $cache = array();
	
	/**
	 * Dynamicly adds a new Behavior to this model. Behaviors should have NO
	 * 'Behavior' at the end of their name (it's automatically added)
	 * 
	 * <code>
	 * $this->User->behavior->add('app.lib.model.behavior.CustomTaggable');
	 * </code>
	 *
	 * @todo maybe create extra class handling the behaviors
	 * @param string $behaviorName
	 * @param array(string) $config
	 * @return ModelBehaviorHandler
	 */
	public function addBehavior($behaviorName, Array $config = array()) 
	{
		$behaviorName = trim($behaviorName);
		if (!isset(self::$cache[$behaviorName])) {
			if ($this->hasBehavior($behaviorName)) return $this;
			if (empty($behaviorName)) throw new ModelEmptyBehaviorNameException($this->model);
			if (substr($behaviorName, 0, -8) !== 'Behavior') {
				$behaviorName .= 'Behavior';
			}
			// Behavior Names as classpaths
			if (strpos($behaviorName, '.') !== false) {
				$behaviorClassName = ClassPath::className($behaviorName);
				$behaviorClassPath = $behaviorClassName;
			} else {
				$behaviorClassName = ucFirst($behaviorName);
				$behaviorClassPath = 'app.lib.model.behavior.'.$behaviorClassName;
				if (!ClassPath::exists($behaviorClassPath)) {
					$behaviorClassPath = 'ephFrame.lib.model.behavior.'.$behaviorClassName;
				}
			}
			// load Behavior Class if not allready loaded
			if (!class_exists($behaviorClassName)) {
				if (!ClassPath::exists($behaviorClassPath)) {
					throw new ModelBehaviorNotFoundException($this->model, $behaviorClassName);
				}
				Library::load($behaviorClassPath);
			}
			self::$cache[$behaviorName] = $behaviorClassName;
		} else {
			$behaviorClassName = self::$cache[$behaviorName];
		}
		$this->behaviors[substr($behaviorClassName, 0, -8)] = new $behaviorClassName($this->model, $config);
		return $this;
	}
	
	/**
	 * Test if the handler has a behavior named $behaviorName added
	 * @return boolean
	 * @param string
	 */
	public function hasBehavior($behaviorName) 
	{
		return isset($this->behaviors[$behaviorName]);
	}
	
	/**
	 * Same as {@link hasBehavior}
	 * 
	 * @param string	$name
	 * @return boolean
	 */
	public function implement($name) 
	{
		return $this->hasBehavior($name);
	}
	
	/**
	 * Removes a behavior from this model by $behaviorName
	 * 
	 * @param string $behaviorName
	 * @return boolean
	 */
	public function removeBehavior($behaviorName) 
	{
		if (isset($this->behaviors[$behaviorName])) {
			unset($this->behaviors[$behaviorName]);
		} elseif (in_array($behaviorName, $this->behaviors)) {
			unset($this->behaviors[array_search($behaviorName, $this->behaviors)]);
		}
		return true;
	}
	
	/**
	 * Trigger a method call on all behaviors if available passing $args as
	 * arguments.
	 * 
	 * @param string $methodName
	 * @param array(string) $args
	 * @return mixed
	 */
	public function trigger($methodName, Array $args = array()) 
	{
		foreach($this->behaviors as $behavior) {
			if (!method_exists($behavior, $methodName)) continue;
			if ($r = $behavior->callMethod($methodName, $args));
		}
		if (isset($r)) {
			return $r;
		} else {
			throw new ModelBehaviorHandlerMethodNotFoundException($this->model, $methodName);
		}
	}
	
	public function __call($methodName, Array $args = array()) 
	{
		if (method_exists($this, $methodName)) {
			return $this->callMethod($methodName, $args);
		}
		return $this->trigger($methodName, $args);
	}
	
	/**
	 * Magic Method call for retreiving a single model behavior by name
	 * <code>
	 *
	 * </code>
	 * @param string	$behaviorName
	 * @return ModelBehavior Model behavior class if found
	 */
	public function __get($behaviorName) 
	{
		if ($this->hasBehavior($behaviorName)) {
			return $this->behaviors[$behaviorName];
		}
		return false;
	}
	
	/**
	 * Returns the number of behaviors
	 * @return integer
	 */
	public function count() 
	{
		return count($this->behaviors);
	}
	
	public function next() 
	{
		return next($this->behaviors);
	}
	
	public function rewind() 
	{
		return reset($this->behaviors);
	}
	
	public function valid() 
	{
		return FALSE !== $this->current();
	}
	
	public function key() 
	{
		return key($this->behaviors);
	}
	
	public function current() 
	{
		return current($this->behaviors);
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.exception
 */
class ModelBehaviorHandlerException extends ObjectException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.exception
 */
class ModelEmptyBehaviorNameException extends ModelBehaviorHandlerException 
{
	public function __construct(Model $model) 
	{
		parent::__construct('Empty model behavior name detected when trying to add a new behavior.');
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.exception
 */
class ModelBehaviorHandlerMethodNotFoundException extends ModelBehaviorHandlerException 
{
	public function __construct(Model $model, $method) 
	{
		parent::__construct('The model \''.get_class($model).'\' does not implement the behavior method "'.$method.'".');
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.exception
 */
class ModelBehaviorNotFoundException extends ModelBehaviorHandlerException 
{
	public function __construct(Model $model, $behaviorName) 
	{
		parent::__construct('Unable to find model behavior: \''.$behaviorName.'\'');
	}
}