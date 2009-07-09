<?php

/**
 * ephFrame: <http://code.moresleep.net/project/ephFrame/>
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

/**
 * File Representing a Class Path of ephFrame
 * 
 * This File is initially loaded when you intiate ephFrame. It is always
 * available and is used to major class loading functionallity of ephFrame.
 * In fact it's used by the {@link ephFrame} {@link loadClass} method.
 * 
 * The Classpath also stores the successfull stranslated paths in {@link cache}
 * use {@link reset} to delete the cache
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 05.06.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 */
class ClassPath extends Object {
	
	/**
	 * Stores all translated classPaths
	 * @var array(string)
	 */
	public static $cache = array();
	
	/**
	 * devider for valid classpaths
	 * @var string
	 */
	public static $classPathDevider = '.';
	
	// regexp that indicates a classpath of ephFrame internal paths
	const REGEXP_FRAME_PATH = '/^(frame|ephframe)/i';
	
	// regexp for valid classpath
	const VALID_CLASS_PATH = '/^([a-zA-Z]+[a-zA-Z0-9_-]*\\.*)+$/';
	
	/**
	 * Deletes the cache
	 * @return boolean
	 */
	public static function reset() {
		self::$cache = array();
	}
	
	/**
	 * Extracts the class/interface name from a class path
	 * @param string $classPath
	 * @return string|boolean
	 * @throws ClassPathEmptyClassNameException
	 * @static
	 */
	public static function className($classPath) {
		if (!is_string($classPath)) throw new StringExpectedException();
		if (($className = strrchr($classPath, '.')) !== false) {
			return substr($className, 1);
		} elseif (empty($classPath)) {
			throw new ClassPathEmptyClassNameException();
		}
		return $classPath;
	}
	
	/**
	 * Translates class path from 'ephFrame.lib.component'
	 * format to a filename
	 * @throws IntrusionException on brakes or control chars in classpath such as the x00 string
	 * @throws ClassPathMalFormedException
	 * @param string $classPath
	 * @param boolean $ignoreCache set to true to ignore the cache
	 * @static
	 * @return string	filename
	 */
	public static function translatePath($classPath, $ignoreCache = false) {
		if (!isset(self::$cache[$classPath]) || $ignoreCache === true) {
			if (preg_match('@[^A-Za-z0-9_. -]+@', $classPath)) throw new IntrusionException();
			// check for valid classPath
			if (!preg_match(self::VALID_CLASS_PATH, $classPath)) throw new ClassPathMalFormedException($classPath); 
			$translatedPath = str_replace('.', DS, $classPath);
			// replace ephFrame, Vendor root with the constants
			if (preg_match(self::REGEXP_FRAME_PATH, $translatedPath)) {
				$translatedPath = FRAME_ROOT.substr($translatedPath, strpos($classPath, '.'));
			} elseif (strncasecmp($translatedPath, 'app', 3) == 0) {
				$translatedPath = APP_ROOT.substr($translatedPath, 4);
			} elseif (strncasecmp($translatedPath, 'vendor', 6) == 0) {
				$translatedPath = VENDOR_ROOT.substr($translatedPath, 7);
			} else {
				throw new ClassPathMalFormedException($classPath);
			}
			$translatedPath .= '.php';
			self::$cache[$classPath] = $translatedPath;
		}
		return self::$cache[$classPath];
	}
	
	/**
	 * Tests if the file represented by this classpath exists
	 * @static
	 * @return boolean
	 */
	public static function exists($classPath) {
		$translatedPath = self::translatePath($classPath);
		if (file_exists($translatedPath) && is_file($translatedPath)) return true;
		return false;
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ClassPathException extends BasicException {}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ClassPathEmptyClassNameException extends ClassPathException {
	public function __construct() {
		$this->message = 'Empty Class Path detected.';
		parent::__construct($this->message);
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ClassPathMalFormedException extends ClassPathException {
	public function __construct($classPath) {
		$this->message = 'The classPath \''.$classPath.'\' is not well formed';
		parent::__construct($this->message);
	}
}