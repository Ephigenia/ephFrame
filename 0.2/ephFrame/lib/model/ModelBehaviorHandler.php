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

/**
 * 	Class for Handling Model Behaviors
 * 
 * 	This class handles the model behaviors for the model class.
 * 	
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.model
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 16.10.2008
 */
class ModelBehaviorHandler extends Object implements Iterator, Countable {

	/**
	 * 	@var array(ModelBehavior)
	 */
	public $behaviors = array();
	
	/**
	 * 	@var Model
	 */
	public $model;
	
	protected $behaviorCallBacks = array('afterConstruct', 'beforeSave', 'afterSave', 'beforeDelete', 'afterDelete', 'beforeFind', 'afterFind', 'beforeInsert', 'beforeUpdate');
	
	public function __construct(Model $model, Array $behaviors = array()) {
		$this->model = $model;
		if (is_array($behaviors)) {
			$this->behaviors = $behaviors;
		}
		$this->initBehaviors();
		return $this;
	}
	
	public function initBehaviors() {
		foreach($this->behaviors as $behaviorName => $config) {
			unset($this->behaviors[$behaviorName]);
			// $behavior = array('Taggable', 'Deletable') notation
			if (is_int($behaviorName)) {
				$behaviorName = $config;
				$config = array();
			// $behavior = array('Taggable' => array('config')); notation
			} elseif (is_string($behaviorName) && is_array($config)) {
					
			// other notations ignored
			} else {
				continue;
			}
			$this->addBehavior($behaviorName, $config);
		}
		return true;
	}
	
	/**
	 * 	Dynamicly adds a new Behavior to this model. Behaviors should have NO
	 * 	'Behavior' at the end of their name (it's automatically added)
	 * 
	 * 	<code>
	 * 	$this->User->behavior->add('app.lib.model.behavior.CustomTaggable');
	 * 	</code>
	 *
	 * 	@todo maybe create extra class handling the behaviors
	 * 	@param string $behaviorName
	 * 	@param array $config
	 */
	public function addBehavior($behaviorName, Array $config = array()) {
		$behaviorName = trim($behaviorName);
		if (empty($behaviorName)) throw new ModelEmptyBehaviorNameException($this);
		// simple behavior names (without dots)
		if (strpos($behaviorName, '.') === false) {
			// search behavior in app
			$behaviorClassName = $behaviorName.'Behavior';
			$behaviorClassPath = 'app.lib.model.behavior.'.$behaviorName.'Behavior';
			if (!class_exists($behaviorClassName) && !ClassPath::exists($behaviorClassPath)) {
				// then search behavior in ephFrame
				$behaviorClassPath = 'ephFrame.lib.model.behavior.'.$behaviorName.'Behavior';
				if (!ClassPath::exists($behaviorClassPath)) {
					throw new ModelBehaviorNotFoundException($this, $behaviorClassName);
				}
				ephFrame::loadClass($behaviorClassPath);
			}
		// behavior names with dots
		} else {
			$behaviorClassPath = $behaviorName.'Behavior';
			$behaviorName = ClassPath::className($behaviorName);
			$behaviorClassName = $behaviorClassName.'Behavior';
			if (!class_exists($behaviorClassName)) {
				try {
					ephFrame::loadClass($behaviorClassPath);
				} catch (ephFrameClassFileNotFoundException $e) {
					throw new ModelBehaviorNotFoundException($this, $behaviorName);
				}
			}
		}
		if (!is_array($config)) {
			$config = array();
		}
		$this->behaviors[$behaviorName] = new $behaviorClassName($this->model, $config);
		return $this;
	}
	
	/**
	 * 	Removes a behavior from this model by $behaviorName
	 * 	@param string $behaviorName
	 * 	@return boolean
	 */
	public function removeBehavior($behaviorName) {
		if (isset($this->behaviors[$behaviorName])) {
			unset($this->behaviors[$behaviorName]);
		} elseif (in_array($behaviorName, $this->behaviors)) {
			unset($this->behaviors[array_search($behaviorName, $this->behaviors)]);
		}
		return true;
	}
	
	public function call($methodName) {
		$args = func_get_args();
		$args = array_slice($args,1);
		$r = $this->__call($methodName, $args);
		return $r;
	}
	
	public function __call($methodName, $args) {
		if (!in_array($methodName, $this->behaviorCallBacks)) {
			trigger_error('Invalid callback method name \''.$methodName.'\'.', E_USER_ERROR);
		}
		foreach($this->behaviors as $behavior) {
			$r = $behavior->callMethod($methodName, $args);
			if (!$r) {
				return $r;
			}
		}
		return true;
	}
	
	public function count() {
		return count($this->behaviors);
	}
	
	public function next() {
		return next($this->behaviors);
	}
	
	public function rewind() {
		return reset($this->behaviors);
	}
	
	public function valid() {
		return FALSE !== $this->current();
	}
	
	public function key() {
		return key($this->behaviors);
	}
	
	public function current() {
		return current($this->behaviors);
	}
	
}

/**
 *	@package ephFrame
 *	@subpackage ephFrame.exception
 */
class ModelBehaviorHandlerException extends ObjectException {}

/**
 *	@package ephFrame
 *	@subpackage ephFrame.exception
 */
class ModelEmptyBehaviorNameException extends ModelBehaviorHandlerException {
	public function __construct(Model $model) {
		parent::__construct('Empty model behavior name detected when trying to add a new behavior.');
	}
}

/**
 *	@package ephFrame
 *	@subpackage ephFrame.exception
 */
class ModelBehaviorNotFoundException extends ModelBehaviorHandlerException {
	public function __construct(Model $model, $behaviorName) {
		parent::__construct('Could not find class for model behaviro: \''.$behaviorName.'\'');
	}
}

?>