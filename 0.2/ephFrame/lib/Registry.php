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
 *	Registry Pattern Implementation
 * 	
 * 	Class designed after the registry design pattern. This is very usefull
 * 	for storing configuration variables.<br />
 * 	<br />
 * 	Small Example:
 * 	<code>
 * 	// setting a configuration variable for a server
 * 	Registry::set('storagePath', '/img/storage/', '/localhost/');
 * 	// this variable is only reachable if the current host is 'localhost'
 * 	echo Registry::get('storagePath');
 * 	</code>
 * 	<br />
 * 
 * 	Setting variables that are valid in local network
 * 	<code>
 * 	Registry::set('contactEmail', 'puto@generalo.fck', '/(192\.168.*|127.1.*)/');
 * 	</code>
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 03.05.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@version 0.2
 */
class Registry extends Object implements Countable {
	
	/**
	 *	stores the properties that have been set in a associative array
	 * 	The Variables are stored like that:
	 * 	$data[(varname)][(default|other regexp] = value
	 *	@var array(array(mixed))
	 */
	public static $data = array ();
	
	/**
	 *	Returns number of registered domains
	 * 	@return integer
	 */
	public function count() {
		return count($this->data);
	}
	
	/**
	 * 	Set a variable, only works if the variable has not 
	 *  set previously, otherwise {@link RegistryVarNotFoundException} is thrown
	 * 	@throws RegistryVarNotFoundException
	 */
	public static function define($varname, $value, $regexp = null) {
		if (self::defined($varname, ($regexp !== null) ? $regexp : null)) throw new RegistryVarAlreadySetException($varname);
		return self::set($varname, $value, $regexp);
	}

	/**
	 * 	Sets a new variable in the data set for this registry. Overwrites
	 * 	variables that have been set before! If you want to be save, use {@link define}
	 *
	 *	@param string	$varname
	 *	@param mixed	$value
	 *	@param string|array(string)	$regexp
	 * 	@return boolean
	 */
	public static function set($varname, $value = null, $regexp = null) {
		if ($regexp === null) {
			self::$data[$varname]['default'] = $value;
		} else {
			if (is_array($regexp)) {
				foreach ($regexp as $value) self::set($varname, $value, $regexp);
			} else {
				if (substr($regexp,0,1) != '/' && substr($regexp, -1, 1) != '/') {
					$regexp = '/'.$regexp.'/';
				}
				self::$data[$varname][$regexp] = $value;
			}
		}
		return true;
	}

	/**
	 * 	Alias for {@link set}
	 * 	@param string	$keyName
	 *	@param mixed	$value
	 *	@param string|array(string)	$domains
	 */
	public static function write($varname, $value, $regexp = null) {
		return self::set($keyName, $value, $regexp);
	}

	/**
	 *	Returns variable for the current domain, except domain does not fit
	 * 	any of the regular expressions set during {@link write} processes. If
	 * 	no regular expression fits, default values will be returned.
	 * 	Otherwise {@link RegistryNotFoundException} is thrown
	 *
	 *	@param	string	$varname
	 *	@return	boolean|mixed
	 *	@throws RegistryNotFoundException
	 * 	@todo re-check the method, default value is always returned first :(
	 */
	public static function get($varname = '') {
		if (!self::defined($varname)) throw new RegistryVarNotFoundException($varname);
		if (isset($_SERVER['SERVER_NAME'])) {
			$matchAgainst = $_SERVER['SERVER_NAME'];
			foreach(self::$data[$varname] as $regexp => $returnValue) {
				if ($regexp !== 'default' && preg_match($regexp, $matchAgainst)) return $returnValue;
			}
		}
		// no match, return default value
		return self::$data[$varname]["default"];
	}
	
	/**
	 * 	Alias for {@link get}
	 * 	@param string $varname
	 */
	public static function read($varname) {
		return self::get($varname);
	}
	
	/**
	 *	Checks if a variable was set
	 *	@param string	$varname
	 *	@param string 	$regexp
	 *	@return boolean
	 */
	public static function defined($varname, $regexp = null) {
		if (!isset(self::$data[$varname])) return false;
		foreach (self::$data[$varname] as $reg => $val) {
			
		}
		if (isset(self::$data[$varname]["default"])) return true;
		return false;
	}
	
	/**
	 *	Clears all vars
	 * 	@return boolean
	 */
	public static function clear() {
		self::$data = array();
		return true;
	}
	
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class RegistryException extends BasicException {}
/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class RegistryVarNotFoundException extends RegistryException {
	public function __construct($varname) {
		parent::__construct(sprintf('Registry var \'%s\' was\'t found in the Registry.', $varname));
	}
}
/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class RegistryVarAlreadySetException extends RegistryException {
	public function __construct($varname) {
		parent::__construct(sprintf('Registry var \'%s\' was allready registered.', $varname));
	}
}

?>