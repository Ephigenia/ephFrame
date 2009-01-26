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

// load parent class
ephFrame::loadClass('ephFrame.lib.component.Hash');

/**
 *	Session Class
 * 
 * 	Delete, Read, Write Session Variables
 * 
 * 	<strong>Note!</strong> that the Session Component can be used only one time
 * 	for a hole application controller. If you extend this class and use Session
 * 	and for example ApplicationSession for one controller they both compete with
 * 	each other.
 * 
 * 	// todo put this as a component
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 02.05.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.helper
 * 	@version 0.1
 */
class Session extends Hash {
	
	/**
	 * 	Session Name that is Used, use
	 * 	the setter function {@link name} for setting
	 * 	a new name or pass the session name to the {@link __construct}
	 *
	 * 	@var string
	 */
	public $name = SESSION_NAME;
	
	/**
	 * 	Creates a new Session, you can pass the sessions Name to
	 * 	the constructor to split session in complex projects
	 *
	 * 	@param 	string	$sessionName
	 * 	@return Session
	 */
	public function init(Controller $controller) {
		$this->start();
		$this->data = &$_SESSION;
		// register session save
		// todo use session_set_save_handler to register current session class
		return parent::init($controller);
	}
	
	/**
	 * 	Starts a session and sets the $sessionName
	 *
	 * 	@param unknown_type $sessionName
	 * 	@return boolean
	 */
	public function start($sessionName = null) {
		if (!empty($sessionName)) {
			$this->name($sessionName);
		} else {
			$this->name($this->name);
		}
		@session_start();
		return true;
	}
	
	/**
	 * 	sets or returns the current session id
	 * 	@param 	string	$id	new session name
	 * 	@throws StringExpectedException
	 * 	@return string
	 */
	public function id($id = null) {
		if (func_num_args() > 0) {
			if (!is_string($id) || strlen($id) == 0) throw new StringExpectedException();
			session_id($id);
		}
		return session_id();
	}
	
	/**
	 * 	Sets or returns the session name that is used for the cookie that
	 * 	stores the session id.
	 * 	@param	string $name	New name
	 * 	@throws StringExpectedException
	 * 	@return string
	 */
	public function name($name = null) {
		if (func_num_args() > 0 && $name !== null) {
			session_name((string) $name);
		}
		return session_name();
	}
	
	/**
	 * 	Saves all Session Variables stored in {@link data} on
	 * 	object destruction
	 *
	 * 	@return boolean
	 */
	public function __destroy() {
		session_write_close();
		parent::__destroy();
		return true;
	}

}

?>