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
 *	Cookie Component Class
 * 
 * 	This is a class that makes it easy to manage cookie data. The Cookies are
 * 	saved right before the controller renders the view (because the controller
 * 	calls beforeRender) or the Cookie component gets destroyed and there are
 * 	still cookies to save. So even when the controller redirects to another
 * 	page all cookies get saved.
 * 	
 * 	The secure mode of setCookie is always used, so all cookies created using
 * 	this component are HTTP-Only. Read more about this here:
 * 	http://en.wikipedia.org/wiki/HTTP_cookie
 *
 * 	<code>
 * 	// set a permanent login cookie that lats one week
 * 	$this->Cookie->write('permanentKey', md5('salt' + $User->id), WEEK);
 * 	</code>
 * 	
 * 	<strong>Cookie-Arrays</strong>
 * 	You can create nested cookies by using the array notation for their name.
 * 	See the example:
 * 	<code>
 * 	$this->Cookie->write('User[id]', 1);
 * 	$this->Cookie->write('User[name]', 'Ephigenia');
 * 	</code>
 * 
 * 	<strong>Cookie on subdomains</strong>
 * 	You can use cookies on all subdomains if you use the $domain attribute:
 * 	<code>
 * 	$this->Cookie->write('permanentKey', 'x', null, null, '.example.com');
 * 	</code>
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 02.05.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.helper
 * 	@version 0.1
 */
class Cookie extends Component {
	
	/**
	 *	Standard Expiration Time for new create cookies
	 * 	that have no duration
	 * 	@var integer
	 */
	public $TTL = WEEK;
	
	/**
	 *	Domain for newly created cookies
	 * 	@var string
	 */
	public $path = '/';
	
	/**
	 *	Domain that is used by default when saving cookies
	 * 	@var string
	 */
	public $domain;
	
	/**
	 *	Enable/disable all cookie data on class destruct
	 * 	@var boolean
	 */
	public $autosave = true;
	
	/**
	 *	Stores all cookies	
	 * 	@var array(string)
	 */
	public $data = array();
	
	/**
	 *	Saves all cookies that should be written
	 * 	@var array(string)
	 */
	protected $saveData = array();
	
	/**
	 * 	Cookie Constructor
	 *	@return Cookie
	 */
	public function __construct() {
		parent::__construct();
		$this->data = &$_COOKIE;
		if (empty($this->domain)) {
			$this->domain = $_SERVER['HTTP_HOST'];
		}
		return $this; 
	}
	
	/**
	 *	Sets or returns new domain for new variables
	 * 	@param string	
	 * 	@return Cookie|string
	 */
	public function domain($domain = null) {
		return parent::__getOrSet('domain', $domain);
	}
	
	/**
	 *	Sets or returns the default path for new cookies
	 * 	@param string
	 * 	@return Cookie|string
	 */
	public function path($path = null) {
		return parent::__getOrSet('path', $path);
	}
	
	/**
	 *	Sets a cookie with the name $varname, the value $value and if you want
	 * 	to also duration time (otherwise default duration will be used) and domain
	 * 
	 * 	Duration is the time the cookie should be active in seconds from now on.
	 * 	You can use the global constants for that:
	 * 	<code>
	 * 		// set user login session for one week
	 * 		$Cookie->write('userId', $userId, WEEK);
	 * 	</code>
	 * 
	 * 	Value is checked for XSS Intrusion
	 * 
	 * 	@param	string	$varname	Cookiename
	 * 	@param	string|integer	$value	
	 *	@param	integer	$ttl	time-to-live for this cookie
	 * 	@param	string $domain 
	 * 	@throws StringExpectedException on invalid varname or value
	 * 	@throws IntegerExpectedException on invalid duration inputs
	 */
	public function write($varname, $value, $ttl = null, $path = null, $domain = null, $secure = true) {
		if (!is_string($varname) || strlen($varname) == 0) throw new StringExpectedException();
		if (is_object($value) || is_array($value)) throw new StringExpectedException();
		$this->data[$varname] = $value;
		$this->saveData[$varname] = array(
			'value' => $value,
			'ttl' => ($ttl === null) ? $this->TTL : $ttl,
			'path' => ($path === null) ? $this->path : $path,
			'domain' => $domain,
			'secure' => $secure
		);
		return true;
	}
	
	public function set($varname, $value, $ttl = null, $path = null, $domain = null, $secure = true) {
		return $this->write($varname, $value, $ttl, $path, $domain, $secure);
	}
	
	/**
	 *	Checks if a cookie variable is set and not expired
	 * 	@param string	$varname
	 * 	@return boolean
	 */
	public function defined($varname) {
		return isset($this->data[$varname]);
	}
	
	/**
	 *	Reads a cookie from the cookies that is named $varname
	 * 	If the cookie with the name $varname is expired false is returned
	 * 	
	 * 	@param	string $varname
	 * 	@return string|integer
	 */
	public function read($varname) {
		if ($this->defined($varname)) {
			return $this->data[$varname];
		}
		return false;
	}
	
	/**
	 *	Alias for {@link read}
	 * 	@param string $varname
	 * 	@return mixed
	 */
	public function get($varname) {
		return read($varname);
	}
	
	/**
	 *	Deletes a cookie
	 * 	@param string	$cookiename
	 * 	@return boolean
	 */
	public function delete($cookiename) {
		if ($this->defined($cookiename)) {
			$this->saveData[$cookiename]['ttl'] = -1;
			unset($this->data[$cookiename]);
		}
		return true;
	}
	
	/**
	 *	Saves all cookies that are new and returns the number of cookies saved
	 * 	@return integer
	 */
	public function save() {
		foreach ($this->saveData as $cookieName => $cookieData) {
			$path = (isset($cookieData['path'])) ? $cookieData['path'] : $this->path;
			$ttl = (isset($cookieData['ttl'])) ? $cookieData['ttl'] : $this->TTL;
			$domain = (isset($cookieData['domain'])) ? $cookieData['domain'] : null;
			$secure = (isset($cookieData['secure'])) ? $cookieData['secure'] : true;
			$value = $cookieData['value'];
			@setcookie($cookieName, $value, time() + $ttl, $path); //, $domain, $secure);
		}
		$count = count($this->saveData);
		$this->saveData = array();
		return $count;
	}
	
	public function beforeRender() {
		if ($this->autosave) {
			$this->save();
		}
	}
	
	public function __destruct() {
		if ($this->autosave && count($this->saveData) > 0) {
			$this->save();
		}
	}
	
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class CookieException extends BasicException {}

?>