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
 *	Validate things
 * 
 * 	Use this class to validate varios kinds of things such as {@link email}s,
 * 	{@link integer}s, {@link isbn} numbers or {@link URL}s.
 *
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 02.05.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.helper
 * 	@version 0.2
 */
class Validator extends Helper {
	
	public $config = array();
	public $callbackObject;
	
	/**
	 *	Creates a new validator
	 * 	
	 * 	@param array(string) $config
	 * 	@param Object $callbackObject
	 */
	public function __construct($config = array(), $callbackObject = null) {
		$this->config = $config;
		$this->callbackObject = $callbackObject;
		return $this;
	}
	
	/**
	 * 	Validates a target value using the $rules passed.
	 * 	This is used in the Model->validate and FormField->validate methods
	 *
	 * 	@param string|mixed $value
	 * 	@return boolean|string
	 */
	public function validate($value = null) {
		// single line string rules are callbacks!
		foreach($this->config as $ruleName => $config) {
			// allowEmpty Rule
			if (isset($config['allowEmpty']) && $config['allowEmpty'] === true && empty($value)) {
				return true;
			}
			// getting failure message
			if (!isset($config['message'])) {
				$message = false;
			} else {
				// replace wildcards in the failmessage
				if (!is_array($config)) {
					$config = array('message' => $config);
				}
				$message = String::substitute($config['message'], array_merge($config, array(
					'value' => $value,
					'rule' => $ruleName,
					'ruleName' => $ruleName,
					'length' => String::length($value),
					'type' => gettype($value)
				)));
			}
			// testing callback if defined
			if (isset($config['callback'])) {
				if (isset($this->callbackObject)
					&& method_exists($this->callbackObject, $config['callback'])
					&& $this->callbackObject->$config['callback']($value) == false) {
					return $message;
				}
			}
			// test other rules
			if (isset($config['regexp']) && !preg_match($config['regexp'], $value)) {
				return $message;
			} elseif (isset($config['notEmpty']) && empty($value)) {
				return $message;
			} elseif (isset($config['maxLength']) && String::length($value) > $config['maxLength']) {
				return $message;
			} elseif (isset($config['minLength']) && String::length($value) < $config['minLength']) {
				return $message;
			} elseif (isset($config['min']) && (float) $value > $config['min']) {
				return $message;
			} elseif (isset($config['max']) && (float) $value > $config['max']) {
				return $message;
			}
		}
		return true;
	}
	
	/**
	 *	Regexp for valid german zip codes 
	 */
	const ZIP_DE = '/^\\d{5}$/';
	/**
	 *	Validates a german zip code
	 *	@param string|integer $zipCode
	 *	@return true if zipCode is a valid German zip code
	 */
	public static function zipDe($zipCode) {
		return preg(self::ZIP_DE, $zipCode);
	}
	 
	/**
	 *	Regular Expression for Email Validation
	 * 	note that this regexp gets almost all wrong email addies, except those
	 * 	with double dots or dots at the end or start of the local part.
	 */
	const EMAIL = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~.-]{1,64}@([a-zA-Z0-9][-a-zA-Z0-9]*[a-zA-Z0-9]\.)+([a-zA-Z0-9]{2,5})$/i';
	
	/**
	 * 	Validates a email addy using regular expression
	 *	@param string $stringEmail
	 *	@return boolean
	 */
	public static function email($stringEmail) {
		return (bool) preg_match(self :: EMAIL, (string) $stringEmail);
	}
	
	/**
	 * 	Regular Expression for matching IP Adresses from 0.0.0.0 to 255.255.255.255
	 *	@var string
	 */
	const IP = '/^([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\\.([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$';
	/**
	 * 	Returns true if the given IP is a valid ip (from 0.0.0.0 to 255.255.255.255)
	 * 	@param string	$ip
	 * 	@return boolean
	 */
	public static function IP($ip) {
		return (preg_match(self :: IP, (string) $ip));
	}

	/**
	 *	Regular Expression for matchin ICQ UINs (1 to 8 Digits)
	 *	@var string
	 */
	const ICQUIN = '/^\\d{1,8}$/';
	/**
	 * 	Return true if $ICQUIN is a valid ICQ User Identification Number
	 *	@param	integer|string	$ICQUIN	
	 *	@return boolean
	 */
	public static function ICQUIN($ICQUIN) {
		if (is_string($ICQUIN) || is_int($ICQUIN)) {
			$ICQUIN = intval($ICQUIN);
			if (!empty ($ICQUIN)) {
				return preg_match(self :: ICQUIN, $ICQUIN);
			}
		}
		return false;
	}
	
	const HOSTNAME = '/^([a-zA-Z0-9][-a-zA-Z0-9]*[a-zA-Z0-9]\.)+([a-zA-Z0-9]{2,5})$/i';
	
	/**
	 * 	Validates a hostname using regular exrepssion
	 * 	@param unknown_type $hostname
	 * 	@return boolean
	 */
	public static function hostname($hostname) {
		return preg_mtach(self::HOSTNAME, $hostname);
	}
	
	/**
	 *	Regular Expression for checking for valid urls
	 *	@var string
	 */
	const URL = '{
  \\b
  # Match the leading part (proto://hostname, or just hostname)
  (
    # http://, or https:// leading part
    (https?)://[-\\w]+(\\.\\w[-\\w]*)+
  |
    # or, try to find a hostname with more specific sub-expression
    (?i: [a-z0-9] (?:[-a-z0-9]*[a-z0-9])? \\. )+ # sub domains
    # Now ending .com, etc. For these, require lowercase
    (?-i: com\\b
        | edu\\b
        | biz\\b
        | gov\\b
        | in(?:t|fo)\\b # .int or .info
        | mil\\b
        | net\\b
        | org\\b
        | [a-z][a-z]\\.[a-z][a-z]\\b # two-letter country code
    )
  )

  # Allow an optional port number
  ( : \\d+ )?

  # The rest of the URL is optional, and begins with /
  (
    /
    # The rest are heuristics for what seems to work well
    [^.!,?;"\'<>()\[\]\{\}\s\x7F-\\xFF]*
    (
      [.!,?]+ [^.!,?;"\'<>()\\[\\]\{\\}\s\\x7F-\\xFF]+
    )*
  )?
}ix';
	/**
	 * 	Returns true if the url is valid or not
	 *	@param	string	$url	
	 *	@return boolean
	 */
	public static function URL($url) {
		return (preg_match(self :: URL, $url));
	}
	
	/**
	 *	Regular Expression for checking for valid dates in format
	 *  dd.mm.yyyy or d.m.yy
	 *	@var string
	 */
	const DATE = '/\\b(0?[1-9]|[12][0-9]|3[01])[- \/.](0?[1-9]|1[012])[- \/.](19|20)?[0-9]{2}\\b/';
	/**
	 *	Returns true if the date is valid or not
	 */
	public static function date($date) {
		return (preg_match(self :: DATE, $date));
	}
		
	/**
	 *	Regular Expression vor validating integers
	 *	@var string
	 */
	const INTEGER = '/^[-+]?\\b\\d+\\b$/';
	/**
	 * 	Regular Expression vor validating integers
	 *	valid formats are +1, -2, 23234 or something like that
	 *	@param mixed	$integer
	 *	@return boolean
	 */
	public static function integer($integer) {
		if (is_int($integer)) return true;
		return (preg_match(self :: INTEGER, (string) $integer));
	}
	
	/**
	 *	Regular Expression vor validating floats
	 *	@var string
	 */
	const FLOAT = '/^[-+]?\\b(?:[0-9]*(\\.|,))?[0-9]+\\b$/';
	/**
	 * 	Checks if the param passed is a valid float
	 * 	valid formats are +0.23, -123,24,039.32 and ,234, BUT NOT: +.32
	 *	@return boolean
	 */
	public static function float($float) {
		if (is_float($float)) return true;
		return (preg_match(self :: FLOAT, (string)$float));
	}
	
	/**
	 *	Validating Timestamps
	 *	@param integer
	 * 	@return boolean
	 */
	public static function timestamp($timestamp) {
		return (self::isInteger($timestamp));
	}
	
	/**
	 *	Matches ISBN Numbers
	 * 	@var string
	 */
	const ISBN = 'ISBN\x20(?=.{13}$)\d{1,5}([- ])\d{1,7}\1\d{1,6}\1(\d|X)$';
	
	/**
	 *	Validating an ISBN Number
	 * 	@param string
	 * 	@return boolean
	 */
	public static function isbn($isbn) {
		return preg_match(self::ISBN, $isbn);
	}
	
}
?>