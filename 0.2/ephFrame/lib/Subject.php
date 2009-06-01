<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright (c) 2007+, Ephigenia M. Eichner
 * 						 Kopernikusstr. 8
 * 						 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 
 * 	@license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright		copyright 2007+, Ephigenia M. Eichner
 * 	@link			http://code.ephigenia.de/projects/ephFrame/
 * 	@version		$Revision$
 * 	@modifiedby		$LastChangedBy$
 * 	@lastmodified	$Date$
 * 	@filesource		$HeadURL$
 */

/**
 * 	Abstract Subject
 * 
 * 	Subject is part of the <a href="http://en.wikipedia.org/wiki/Observer_pattern">Observer Design Pattern</a>
 * 
 *	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 16.07.2007
 * 	@version 1.0
 * 	@abstract 
 */
abstract class Subject extends Object implements SplSubject {
	
	/**
	 * 	List of observers registered by this subject
	 * 	@var unknown_type
	 */
	protected $observers = array();
 
	/**
	 * 	Attaches a new Observer
	 *	@param Observer $observer
	 */
	public function attach(Observer $observer) {
		$this->observers[] = $observer;
	}
 
	/**
	 * 	Detaches an observer
	 * 	@param Observer $observer
	 * 	@return boolean 
	 */
	public function detach(Observer $observer) {
		for ($i = 0; $i < sizeof($this->observers); $i++) {
			if ($this->observers[$i] === $observer) {
				unset($this->observers[$i]);
				return true;
			}
		}
		return false;
	}
 
	/**
	 * 	Sends a update method call to all observers
	 * 	@return Subject
	 */
	protected function notify() {
		for ($i = 0; $i < count($this->observers); $i++) {
			$this->observers[$i]->update();
		}
		return $this;
	}
 
	public abstract function getState();
	
	public function __clone() {
		throw new NotClonableException();
	}
	
}

?>