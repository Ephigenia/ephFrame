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

// Load String Class that is needed
ephFrame::loadClass('ephFrame.lib.helper.String');

/**
 *	Sanitize Helper Class
 * 	
 * 	Class for sanitizing user inputs. This works more like
 * 	a blacklist filter for strings. Cutting all dangerous
 * 	strings such as null strings (0x00) and tags and so on.
 * 
 * 	Please notice that is class is always hard in work,
 * 	there may be some leaks remaining. If you find an security
 * 	bug (a character or a line) that is not captured by this class
 * 	write me!
 * 
 * 	Some things about this class, as you see especially the name
 * 	was taken from the free <a href="http://www.cakephp.org">cake framework</a>
 * 
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.helper
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 10.06.2007
 * 	@uses String
 */
class Sanitize extends Helper {
	
	/**
	 *	Sanitizes a string by stripping almost every
	 * 	ambigious and dangerous parts.
	 * 	
	 * 	This function works with a pointer to the actually
	 * 	string, so your actual variable value is manipulated:
	 * 	<code>
	 * 		$string = '<hello>";
	 * 		Sanitize::panic($string);
	 * 		// will echo 'hello'
	 * 		echo $string;
	 * 	</code>
	 * 
	 * 	@param string $string
	 * 	@return string 
	 */
	public static function panic(&$string) {
		if (is_object($string)) {
			throw new TypeException();
		}
		if (is_array($string)) {
			return self::panicArray($string);
		}
		$sanitized = $string;
		$sanitized = trim($string);
		$sanitized = String::stripTags($string);
		return $sanitized;
	}
	
	/**
	 *	Sanitizes an array with the help of {@link panic}
	 * 
	 * 	Please notice that the actual array values are manipulated,
	 * 	see the code example in {@link panic}
	 * 
	 * 	@param array(string)
	 * 	@return array(string)
	 */
	public static function panicArray(&$array) {
		if (!is_array($array)) {
			throw new ArrayExpectedException();
		}
		$ret = array();
		foreach ($array as $key => $value) {
			$array[$key] = self::panic($value);
		}
		$array = $ret;
		return $array;
	}
	
	/**
	 * 	Strips everything that is not a letter, number, underscore (_) or minus
	 *	With this method used on any input you'll get probably save data. Even
	 * 	Filenames should be very save after that.
	 * 
	 * 	@param string $string
	 * 	@return string
	 */
	public static function paranoid($string) {
		if (!is_string($string) && is_numeric($string)) return '';
		return preg_replace('/[^-a-z0-9_]/i', '', $string);
	}
	
}
?>