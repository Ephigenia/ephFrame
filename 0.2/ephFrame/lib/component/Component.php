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

require_once dirname(__FILE__).'/ephFrameComponent.php';

/**
 * 	Abstract Component Class
 * 
 * 	Components do:
 *  	- help a controller
 * 		- help a model (should be static maybe)
 * 
 *  Components should not:
 * 		- Do basic stuff like lower every string or convert something into an
 * 		  other that has nothing to do with model or controller
 * 	
 * 	All Components in the Application and the Framework are children of this
 * 	class.
 * 
 * 	If a component uses some other components you add them in {@link components}
 * 	and they are alle attached in $this->components and can be reached by
 * 	$this->[componentName].
 * 
 * 	All controllers are created by a controller (or should be) and the call 
 * 	stack looks like that:
 * 		- construct component
 * 		- init component calling {@link init}
 * 		- startup component by calling {@link startup}
 * 
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 06.05.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component
 */
abstract class Component extends Object implements ephFrameComponent {
	
	/**
	 *	List of components that are used by this component. Each of them
	 * 	is attachd to the controller as well.
	 * 	@var array(string)
	 */
	public $components = array();
	
	/**
	 *	Stores a link to the controller this component is attached to, after	
	 * 	{@link startup} is called
	 * 	@var Controller
	 */
	public $controller;
	
	/**
	 *	Every sub-class should call the constructor and always return a instance
	 * 	to the Component
	 * 	@return Component
	 */
	public function __construct() {
		return $this;
	}
	
	/**
	 *	This is called right after a controller constructed a new Component.
	 * 	@param Controller $controller
	 * 	@final 
	 */
	final public function init($controller) {
		logg(Log::VERBOSE_SILENT, 'ephFrame: Component init signal received: \''.get_class($this).'\' ...');
		$this->controller = $controller;
		$this->initComponents();
		return $this;
	}
	
	/**
	 * 	Initiates all components defined for this component and creates a link
	 * 	to them on the component.
	 * 	
	 * 	@return true
	 */
	private function initComponents() {
		// merge list of components from parent component class
		$parentClass = get_parent_class($this);
		$parentClassVars = get_class_vars($parentClass);
		$this->components = array_merge($parentClassVars['components'], $this->components);
		foreach ($this->components as $componentName) {
			$componentClassName = ClassPath::className($componentName);
			// component not attached to controller, therefore do that now 
			if (empty($this->controller->{$componentClassName})) {
				$this->controller->addComponent($componentName);
			}
			$this->{$componentClassName} = $this->controller->{$componentClassName};
			//$this->{$componentClassName}->startup();
		}
		return true;
	}
	
	/**
	 *	As soon the controller has all components created and called {@link init}
	 * 	on everyone, this method is called.
	 * 	@return Controller
	 */
	public function startup() {
		logg(Log::VERBOSE_SILENT, 'ephFrame: Component startup signal received: \''.get_class($this).'\'.');
		return $this;
	}
	
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception 
 */
class ComponentException extends BasicException {}

?>