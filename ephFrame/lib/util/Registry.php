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

class_exists('ArrayHelper') or require dirname(__FILE__).'/ArrayHelper.php';

/**
 * Registry Pattern Implementation
 * 
 * The Registry pattern describes a global accessible class that can store
 * classes or configuration vars.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 03.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @version 0.3
 * @uses ArrayHelper
 */
class Registry
{
	/**
	 * Storage array
	 * @var array(string)
	 */
	public static $data = array();
	
	/**
	 * @param string $key
	 * @param mixed	$value
	 * @param string|array(string) $regexp
	 * @return boolean
	 */
	public static function set($key, $value = null, $regexp = null)
	{
		$regexp = coalesce($regexp, 'default');
		// @todo implement better set method
		$path = explode('.', $key);
		switch (count($path)) {
			case 1:
			default:
				self::$data[$regexp][$path[0]] = $value;
				break;
			case 2:
				self::$data[$regexp][$path[0]][$path[1]] = $value;
				break;
			case 3:
				self::$data[$regexp][$path[0]][$path[1]] = $value;
				break;
			case 4:
				self::$data[$regexp][$path[0]][$path[1]] = $value;
				break;
			case 5:
				self::$data[$regexp][$path[0]][$path[1]] = $value;
				break;
		}
		return true;
	}

	/**
	 * Returns variable for the current domain, except domain does not fit
	 * any of the regular expressions set during {@link write} processes. If
	 * no regular expression fits, default values will be returned.
	 * Otherwise {@link RegistryNotFoundException} is thrown
	 *
	 * @param string $key
	 * @param string $regexp
	 * @return boolean|mixed
	 */
	public static function get($key, $regexp = null)
	{
		$regexp = coalesce($regexp, '/default/');
		foreach(self::$data as $matchAgainst => $data) {
			if (preg_match($regexp, $matchAgainst)) {
				return ArrayHelper::extract(self::$data, $matchAgainst.'.'.$key, '.');
			}
		}
		return null;
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class RegistryException extends BasicException 
{}