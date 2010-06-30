<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

class_exists('IndexedArray') or require dirname(__FILE__).'/../IndexedArray.php';

/**
 * Abstract Observer Pattern integration
 * 
 * Observer is Part of the <a href="http://en.wikipedia.org/wiki/Observer_pattern">Observer Design Pattern</a>.
 * All concrete Observers schould be children of this class.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @author Martin Fleck
 * @since 06.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @abstract 
 */
abstract class Observer extends AppComponent implements SplObserver
{
	/**
	 * List of Event Listeners
	 * @var IndexedArray
	 */
	private $listeners = array();
	
	public function __construct() 
	{
		$this->listeners = new IndexedArray();
		return $this;
	}
	
	/**
	 * Adds a Listener
	 * @param Object $listener
	 * @return EventDispatcher
	 */
	final public function addListener(Object $listener) 
	{
		$this->listeners->add($listener);
		return $this;
	}
	
	/**
	 * removes a listener from the list of current registered listeners
	 * if listener is not found false is returned
	 * @param Object $listener
	 * @return boolean
	 */
	final public function removeListener(Object $listener) 
	{
		foreach($this->listeners as $key => $l) {
			if ($l == $listener) {
				$this->listeners->remove($key);
			}
		}
		return false;
	}
	
	/**
	 * Update callback which is called after notify action of listerns
	 */
	public function update() 
	{
		
	}
	
	/**
	 * Calls an event method on all registered listeners
	 * An Example:
	 * <code>
	 * $loginListener->dispatchEven('loginSuccess', $param1, $param2); 
	 * </code>
	 * @param string $methodName
	 */
	final public function dispatchEvent($methodName, $params = null) 
	{
		if ($params === null) $params = array_slice(func_get_args(),1);
		foreach ($this->listeners as $listener) {
			call_user_func_array(array($listener, $methodName), $params);
		}
	}
	
	/**
	 * Alias for {@link dispatchEvent}
	 */
	final public function triggerEvent($methodName) 
	{
		$this->dispatchEvent($methodName, array_slice(func_get_args(),1));
	}
}