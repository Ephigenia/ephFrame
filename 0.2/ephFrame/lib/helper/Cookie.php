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
 *	Cookie Helper Class for dealing with cookies
 * 
 * 	Cookie Values are checked against possible XSS Intrusion by
 * 	injecting xss into cookie values when you use {@link write} and {@link read}
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 02.05.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.helper
 * 	@version 0.1
 */
class Cookie extends Hash {
	
	/**
	 *	Standard Expiration Time for new create cookies
	 * 	that have no duration
	 * 	@var integer
	 */
	public $expire;
	
	/**
	 *	Domain for newly created cookies
	 * 	@var string
	 */
	public $domain = '/';
	
	/**
	 * 	Cookie Constructor
	 *	@return Cookie
	 */
	public function __construct() {
		parent::__construct();
		$this->data = $_COOKIES;
		$this->expire = time() + DAY;
		return $this; 
	}
	
	/**
	 *	Sets or returns new domain for new variables
	 * 	@param string	
	 * 	@return Cookie|string
	 */
	public function domain($domain = null) {
		if (func_num_args() == 0) return $this->domain;
		$this->domain = $domain;
		return $this;
	}
	
	/**
	 * 	Sets or returns current expiration time for new cookies
	 * 	that have no duration time passed
	 * 
	 * 	@param integer $expiration
	 * 	@throws IntegerExpectedException
	 * 	@return integer
	 */
	public function expire($expire = null) {
		if (func_num_args() > 0) {
			if (is_int($expire)) throw new IntegerExpectedException();
			$this->expire = $expire;
		}
		return $this->expire;
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
	 *	@param	integer	$duration	Time this cookie is active from now in seconds
	 * 	@param	string $domain 
	 * 	@throws StringExpectedException on invalid varname or value
	 * 	@throws IntegerExpectedException on invalid duration inputs
	 */
	public function write($varname, $value, $duration = null, $domain = null) {
		if (!is_string($varname) || strlen($varname) == 0) throw new StringExpectedException();
		if (is_object($value) || is_array($value)) throw new StringExpectedException();
		if ($duration !== null && !is_int($duration)) throw new IntegerExpectedException();
		if (eregi('^[A-Za-z0-9 ]+', $value)) throw new CookieXSSIntrusionException();
		if ($domain !== null) $domain = $this->domain;
		$this->data[$varname] = array(
			'value' => $value,
			'expire' => ($duration === null) ? time() + $duration : $this->expire,
			'domain' => $domain
		);
		return true;
	}
	
	/**
	 *	Checks if a cookie variable is set and not expired
	 * 	@param string	$varname
	 * 	@return boolean
	 */
	public function defined($varname) {
		if (isset($this->data[$varname])) {
			// only return cookie value if not expired!
			if ($this->data[$varname]['expire'] > time()) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 *	Reads a cookie from the cookies that is named $varname
	 * 	If the cookie with the name $varname is expired false is returned
	 * 	
	 * 	@param	string $varname
	 * 	@return string|integer
	 * 	@throws CookieXSSIntrusionException if value contents possible XSS Attack
	 */
	public function read($varname) {
		if ($this->defined($varname)) {
			if (eregi('^[A-Za-z0-9 ]+', $varname)) throw new CookieXSSIntrusionException();
			return $this->data[$varname]['value'];
		}
		return false;
	}
	
	/**
	 *	Deletes a cookie
	 * 	@param string	$cookiename
	 * 	@return boolean
	 */
	public function delete($cookiename) {
		if ($this->defined($varname)) {
			$this->data[$varname]['expire'] = -1;
		}
		return true;
	}
	
	/**
	 *	IMPROVE Maybe inlcude anti-XSS Script into this, value and name of cookie checking
	 */
	private function save() {
		foreach ($this->data as $cookieName => $cookieData) {
			if (isset($cookieData['domain'])) {
				$domain = $cookieData['domain'];
			} else {
				$domain = $this->domain;
			}
			if (isset($cookieData['expire'])) {
				$expire = $cookieData['expire'];
			} else {
				$expire = $this->expire;
			}
			$data = $cookieData['value'];
			setcookie($cookieName, $data, $domain, $expire);
		}
		return true;
	}
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class CookieException extends BasicException {}
/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class CookieXSSIntrusionException extends XSSException {
}

?>