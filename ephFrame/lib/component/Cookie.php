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
 * 	public function login() {
 * 		if ($this->params['username'] == 'alpha' && $this->params['password'] == 'gamma') {
 * 			$this->Cookie->set('welcomeMessage', 'Hi Baby!');
 * 		}
 * 	}
 * }
 * </code>
 *
 * <code>
 * // set a permanent login cookie that lats one week
 * $this->Cookie->set('permanentKey', md5('salt' + $User->id), WEEK);
 * </code>
 * 
 * Saving Arrays
 * 
 * You can create nested cookies by using the array notation for their name.
 * See the example:
 * <code>
 * $this->Cookie->set('User[id]', 1);
 * $this->Cookie->set('User[name]', 'Ephigenia');
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
 * @version 0.2
 */
class Cookie extends AppComponent 
{	
	/**
	 * flag for setting HTTP_ONLY cookies
	 * @var integer
	 */
	const FLAG_HTTPONLY = 1;
	
	/**
	 * use secure setting when setting cookie
	 * @var integer
	 */
	const FLAG_SECURE = 2;
	
	/**
	 * default time to life for new cookies
	 * @var integer
	 */
	public $ttl = WEEK;
	
	/**
	 * default path setting for new cookies
	 * @var string
	 */
	public $path = '/';
	
	/**
	 * default domain setting for new cookies
	 * @var string
	 */
	public $domain;
	
	/**
	 * internal storage for all cookies
	 * @var array(string)
	 */
	public $data = array();
	
	/**
	 * Cookie Constructor
	 * @return Cookie
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->data = &$_COOKIE;
		// deserialize arrays
		foreach($this->data as $k => $v) {
			if (is_string($v) && substr($v, 0, 2) == 'a:') {
				$this->data[$k] = unserialize(stripslashes($v));
			}
		}
		// set domain to current domain as default
		if (empty($this->domain) && isset($_SERVER['HTTP_HOST'])) {
			$this->domain = '.'.$_SERVER['HTTP_HOST'];
		}
		return $this; 
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
	public function set($name, $value, $ttl = null, $path = null, $domain = null, $flags = self::FLAG_HTTPONLY) 
	{
		if ($ttl === null) {
			$ttl = $this->ttl;
		}
		if (is_string($ttl)) {
			$death = strtotime($ttl);
		} else {
			$death = time() + $ttl;
		}
		return setcookie(
			(string) $name,
			is_array($value) ? serialize($value) : $value,
			$death,
			$path === null ? $this->path : $path,
			$domain === null ? $this->domain : $domain,
			(bool) ($flags & self::FLAG_SECURE),
			(bool) ($flags & self::FLAG_HTTPONLY)
		);
	}
	
	/**
	 * Checks if a cookie variable is set and not expired
	 * @param string	$varname
	 * @return boolean
	 */
	public function defined($name) 
	{
		return isset($this->data[$name]);
	}
	
	/**
	 * Reads a cookie from the cookies that is named $varname
	 * If the cookie with the name $varname is expired false is returned
	 * 
	 * @param	string $varname
	 * @return string|integer
	 */
	public function get($name) 
	{
		if ($this->defined($name)) {
			return $this->data[$name];
		}
		return false;
	}
	
	/**
	 * Deletes a cookie
	 * @param string	$cookiename
	 * @return boolean
	 */
	public function delete($name) 
	{
		if ($this->defined($name)) {
			unset($this->data[$name]);
			setcookie($name, false, -1, '/');
		}
		return $this;
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class CookieException extends BasicException 
{}