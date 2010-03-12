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

/**
 * Cookie Component Class
 * 
 * This is a class that makes it easy to manage cookie data. The Cookies are
 * saved right before the controller renders the view (because the controller
 * calls beforeRender) or the Cookie component gets destroyed and there are
 * still cookies to save. So even when the controller redirects to another
 * page all cookies get saved.
 * 
 * The secure mode of setCookie is always used, so all cookies created using
 * this component are HTTP-Only. Read more about this here:
 * http://en.wikipedia.org/wiki/HTTP_cookie
 * 
 * So her now a slightly simple example how to set a cookie from a controller
 * a very year low level example but illustration how to access cookie vars.
 * <code>
 * class ExampleController extends AppController {
 * 	public $components = array('Cookie');
 * 	public function login() 
	{
 * 		if ($this->params['username'] == 'alpha' && $this->params['password'] == 'gamma') {
 * 			$this->Cookie->set('welcomeMessage', 'Hi Baby!');
 * 		}
 * 	}
 * }
 * </code>
 *
 * <code>
 * // set a permanent login cookie that lats one week
 * $this->Cookie->write('permanentKey', md5('salt' + $User->id), WEEK);
 * </code>
 * 
 * Saving Arrays
 * 
 * You can create nested cookies by using the array notation for their name.
 * See the example:
 * <code>
 * $this->Cookie->write('User[id]', 1);
 * $this->Cookie->write('User[name]', 'Ephigenia');
 * </code>
 * 
 * Cookies for subdomains only and every subdomain
 * 
 * You can use cookies on all subdomains if you use the $domain attribute:
 * <code>
 * $this->Cookie->write('permanentKey', 'x', null, null, '.example.com');
 * </code>
 * 
 * Deleting Cookies
 * 
 * <code>
 * $this->Cookie->delete('cookiename');
 * </code>
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 02.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 * @version 0.1
 */
class Cookie extends AppComponent 
{	
	/**
	 * Use this when setting a cockie for http only
	 * @var integer
	 */
	const FLAG_HTTPONLY 	= 1;
	
	/**
	 * Use when setting a cookie
	 * @var integer
	 */
	const FLAG_SECURE 	= 2;
	
	/**
	 * Standard Expiration Time for new create cookies
	 * that have no duration
	 * @var integer
	 */
	public $ttl = WEEK;
	
	/**
	 * Domain for newly created cookies
	 * @var string
	 */
	public $path = '/';
	
	/**
	 * Domain that is used by default when saving cookies
	 * @var string
	 */
	public $domain;
	
	/**
	 * Enable/disable all cookie data on class destruct
	 * @var boolean
	 */
	public $autosave = true;
	
	/**
	 * Stores all cookies	
	 * @var array(string)
	 */
	public $data = array();
	
	/**
	 * Saves all cookies that should be written
	 * @var array(string)
	 */
	protected $saveData = array();
	
	/**
	 * Cookie Constructor
	 * @return Cookie
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->data = &$_COOKIE;
		foreach($this->data as $k => $v) {
			if (is_string($v) && substr($v, 0, 2) == 'a:') {
				$this->data[$k] = unserialize(stripslashes($v));
			}
		}
		if (empty($this->domain) && isset($_SERVER['HTTP_HOST'])) {
			$this->domain = $_SERVER['HTTP_HOST'];
		}
		return $this; 
	}
	
	/**
	 * Sets or returns new domain for new variables
	 * @param string	
	 * @return Cookie|string
	 */
	public function domain($domain = null) 
	{
		return parent::__getOrSet('domain', $domain);
	}
	
	/**
	 * Sets or returns the default path for new cookies
	 * @param string
	 * @return Cookie|string
	 */
	public function path($path = null) 
	{
		return parent::__getOrSet('path', $path);
	}
	
	/**
	 * Sets a cookie with the name $varname, the value $value and if you want
	 * to also duration time (otherwise default duration will be used) and domain
	 * 
	 * Duration is the time the cookie should be active in seconds from now on.
	 * You can use the global constants for that:
	 * <code>
	 * 	// set user login session for one week
	 * 	$Cookie->write('userId', $userId, WEEK);
	 * 	// or 256 days
	 * 	$Cookie->write('userId', $userId, '+256days');
	 * </code>
	 * 
	 * $varname and $value are checked to be proper string values, otherwise an
	 * {@link StringExpectedException} will be thrown.
	 * 
	 * @param	string	$varname		Cookiename
	 * @param	scalar	$value	
	 * @param	integer	$ttl			Time to live for this cookie, a timestamp in the future or a string
	 * @param	string	$domain		Domain String where cookie should be set
	 * @param integer $flags		Set specific flags for the cookie like COOKIE_SECURE or COOKIE_HTTPONLY
	 * @param boolean $httpOnly	 
	 * @throws StringExpectedException on invalid varname or value
	 */
	public function write($varname, $value, $ttl = null, $path = null, $domain = null, $flags = null) 
	{
		if (!is_string($varname) || strlen($varname) == 0) throw new StringExpectedException();
		$this->data[$varname] = $value;
		$this->saveData[$varname] = array(
			'value' => $value,
			'ttl' => $ttl,
			'path' => $path,
			'domain' => $domain,
			'flags' => (int) $flags
		);
		return $this;
	}
	
	/**
	 * Alias for {@link write}
	 * @return Cookie
	 */
	public function set($varname, $value, $ttl = null, $path = null, $domain = null, $flags = null) 
	{
		return $this->write($varname, $value, $ttl, $path, $domain, $flags);
	}
	
	/**
	 * Checks if a cookie variable is set and not expired
	 * @param string	$varname
	 * @return boolean
	 */
	public function defined($varname) 
	{
		return isset($this->data[$varname]);
	}
	
	/**
	 * Reads a cookie from the cookies that is named $varname
	 * If the cookie with the name $varname is expired false is returned
	 * 
	 * @param	string $varname
	 * @return string|integer
	 */
	public function read($varname) 
	{
		if ($this->defined($varname)) {
			if (empty($this->data[$varname])) return false;
			return $this->data[$varname];
		}
		return false;
	}
	
	/**
	 * Alias for {@link read}
	 * @param string $varname
	 * @return mixed
	 */
	public function get($varname) 
	{
		return $this->read($varname);
	}
	
	/**
	 * Deletes a cookie
	 * @param string	$cookiename
	 * @return boolean
	 */
	public function delete($cookiename) 
	{
		if ($this->defined($cookiename)) {
			$this->saveData[$cookiename]['ttl'] = -1;
			unset($this->data[$cookiename]);
			unset($_COOKIE[$cookiename]);
		}
		return $this;
	}
	
	/**
	 * Saves all cookies that are new and returns the number of cookies saved
	 * @return integer
	 */
	public function save() 
	{
		$debug = false;
		foreach ($this->saveData as $cookieName => $cookieData) {
			$path = (isset($cookieData['path'])) ? $cookieData['path'] : $this->path;
			$ttl = (isset($cookieData['ttl'])) ? $cookieData['ttl'] : $this->ttl;
			if (is_string($ttl)) {
				$death = strtotime($ttl);
			} else {
				$death = time() + $ttl;
			}
			$domain = (isset($cookieData['domain'])) ? $cookieData['domain'] : null;
			$secure = (isset($cookieData['flags'])) ? $cookieData['flags'] & self::FLAG_SECURE : false;
			$httpOnly = (isset($cookieData['flags'])) ? $cookieData['flags'] & self::FLAG_HTTPONLY : false;
			$value = @$cookieData['value'];
			if (!empty($debug)) {
				echo '<pre>setting '.$cookieName.': '.$cookieData['value'].' (ttl: '.$ttl.' - till: '.date('d.m.Y H:i', $death).')</pre>';
			}
			if (is_array($value)) {
				$value = serialize($value);
			}
			setcookie($cookieName, $value, $death, $path, $domain, $secure, $httpOnly);
		}
		if (!empty($debug)) die('debug set to true in Cookie->save()');
		$count = count($this->saveData);
		$this->saveData = array();
		return $count;
	}
	
	public function beforeRender() 
	{
		if ($this->autosave) {
			$this->save();
		}
		return parent::beforeRender();
	}
	
	public function __destruct() 
	{
		if ($this->autosave && count($this->saveData) > 0 && !headers_sent()) {
			$this->save();
		}
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class CookieException extends BasicException 
{}