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
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

class_exists('Hash') or require dirname(__FILE__).'/../Hash.php';

/**
 * Session Class
 * 
 * Delete, Read, Write Session Variables
 * 
 * <strong>Note!</strong> that the Session Component can be used only one time
 * for a hole application controller. If you extend this class and use Session
 * and for example ApplicationSession for one controller they both compete with
 * each other.
 * 
 * // todo put this as a component
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 02.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 * @version 0.1
 */
class Session extends Hash {
	
	/**
	 * Session Name that is Used, use
	 * the setter function {@link name} for setting
	 * a new name or pass the session name to the {@link __construct}
	 *
	 * @var string
	 */
	public $name = SESSION_NAME;
	
	/**
	 * Stores the time to live for a session in seconds. You can set
	 * this to your custom value in your projects to increase session lifetime.
	 * @var integer
	 */
	public $ttl;
	
	/**
	 * Components used by session component
	 * @var array(string)
	 */
	public $components = array(
		'Cookie'
	);
	
	/**
	 * Creates a new Session, you can pass the sessions Name to
	 * the constructor to split session in complex projects
	 *
	 * @param 	string	$sessionName
	 * @return Session
	 */
	public function init(Controller $controller) {
		$this->start();
		$this->data = &$_SESSION;
		if (!$this->ttl) {
			$this->ttl = (int) ini_get('session.gc_maxlifetime');
		} else {
			ini_set('session.gc_maxlifetime', $this->ttl);
		}
		// register session save
		// todo use session_set_save_handler to register current session class
		return parent::init($controller);
	}
	
	/**
	 * Starts a session and sets the $sessionName
	 *
	 * @param unknown_type $sessionName
	 * @return boolean
	 */
	public function start($sessionName = null) {
		if (!empty($sessionName)) {
			$this->name($sessionName);
		} else {
			$this->name($this->name);
		}
		if (!empty($this->controller->request->data[$this->name])) {
			$this->id($this->controller->request->data[$this->name]);
		}
		if (!isset($_SESSION)) {
			if (!session_start()) {
				throw new SessionStartException();
			}
		}
		return true;
	}
	
	/**
	 * sets or returns the current session id
	 * @param 	string	$id	new session name
	 * @throws StringExpectedException
	 * @return string
	 */
	public function id($id = null) {
		if (func_num_args() > 0) {
			if (!is_string($id) || strlen($id) == 0) throw new StringExpectedException();
			session_id($id);
		}
		return session_id();
	}
	
	/**
	 * Sets or returns the session name that is used for the cookie that
	 * stores the session id.
	 * @param	string $name	New name
	 * @throws StringExpectedException
	 * @return string
	 */
	public function name($name = null) {
		if (func_num_args() > 0 && $name !== null) {
			$this->name = (string) $name;
			session_name($this->name);
		}
		return session_name();
	}
	
	/**
	 * Session cookie is refreshed with lifetime each time before views
	 * are rendered.
	 * @return Session
	 */
	public function beforeRender() {
		$this->Cookie->set($this->name(), $this->id(), $this->ttl);
		return parent::beforeRender();
	}
	
	/**
	 * Saves all Session Variables stored in {@link data} on
	 * object destruction
	 *
	 * @return boolean
	 */
	public function __destroy() {
		session_write_close();
		parent::__destroy();
		return true;
	}

}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exceptions
 * @author Ephigenia // Marcel Eichner <love@ephigenia.de>
 * @since 16.04.2009
 */
class SessionException extends ComponentException {}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exceptions
 * @author Ephigenia // Marcel Eichner <love@ephigenia.de>
 * @since 16.04.2009
 */
class SessionStartException extends SessionException {
	public function __construct() {
		parent::__construct('Unable to start session: \''.error_get_last().'\'');
	}
}