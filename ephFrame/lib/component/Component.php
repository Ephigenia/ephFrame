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
 * Abstract Component Class
 * 
 * Components do:
 * - help a controller
 * - help a model (should be static maybe)
 * 
 * Components should not:
 * 	- Do basic stuff like lower every string or convert something into an
 * 	  other that has nothing to do with model or controller
 * 
 * All Components in the Application and the Framework are children of this
 * class.
 * 
 * If a component uses some other components you add them in {@link components}
 * and they are alle attached in $this->components and can be reached by
 * $this->[componentName].
 * 
 * All controllers are created by a controller (or should be) and the call 
 * stack looks like that:
 * 	- construct component
 * 	- init component calling {@link init}
 * 	- startup component by calling {@link startup}
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 06.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 */
abstract class Component extends Object
{	
	/**
	 * List of components that are used by this component. Each of them
	 * is attachd to the controller as well.
	 * @var array(string)
	 */
	public $components = array();
	
	/**
	 * List of helpers used in this component
	 * @var array(string)
	 */
	public $helpers = array();
	
	/**
	 * Stores a link to the controller this component is attached to, after	
	 * {@link startup} is called
	 * @var Controller
	 */
	public $controller;
	
	/**
	 * Every sub-class should call the constructor and always return a instance
	 * to the Component
	 * @return Component
	 */
	public function __construct() 
	{
		$this->__mergeParentProperty('components');
		$this->__mergeParentProperty('helpers');
		return $this;
	}
	
	/**
	 * This is called right after a controller constructed a new Component.
	 * @param Controller $controller
	 * @return Component
	 * @final 
	 */
	public function init(Controller $controller) 
	{
		$this->controller = $controller;
		$controller->registerCallback('beforeRender', array($this, 'beforeRender'));
		$controller->registerCallback('afterRender', array($this, 'afterRender'));
		$controller->registerCallback('beforeAction', array($this, 'beforeAction'));
		$controller->registerCallback('afterAction', array($this, 'afterAction'));
		$controller->registerCallback('beforeRedirect', array($this, 'beforeRedirect'));
		foreach($this->helpers as $helper) {
			$this->{$helper} = $controller->addHelper($helper);
		}
		foreach($this->components as $component) {
			$this->{$component} = $controller->addComponent($component);
		}
		return $this;
	}
	
	/**
	 * As soon the controller has all components created and called {@link init}
	 * on everyone, this method is called.
	 * @return Controller
	 */
	public function startup() 
	{
		return $this;
	}
	
	/**
	 * Called by the controller before he renders
	 * @return true
	 */
	public function beforeRender() 
	{
		$this->controller->data->set(get_class($this), $this);
		return true;
	}
	
	/**
	 * Called right after the controller rendered everything, add something
	 * to the rendered output here in your own components. This method should
	 * always return the rendered content to the controller.
	 * @param string $string
	 * @return string
	 */
	public function afterRender($string) 
	{
		return $string;
	}
	
	/**
	 * Callback that is called right before controller calls his action
	 * @return boolean
	 */
	public function beforeAction() 
	{
		return true;
	}
	
	/**
	 * Called before beforeRedirect in the controller continues to send
	 * redirection headers. The return values will do nothing in the controller
	 * @return boolean
	 */
	public function beforeRedirect($url, $status = 'p', $exit = true) 
	{
		return true;
	}
	
	/**
	 * Callback that is calld after action was performed in the controller
	 * @return boolean
	 */
	public function afterAction() 
	{
		return true;
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class ComponentException extends BasicException 
{}