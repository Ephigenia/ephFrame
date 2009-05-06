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
 * 	String/Class Inflector class
 * 
 * 	This class singularizes, pluralizes from Strings to Classnames and from
 * 	Classnames to strings. Used a lot in the model classes.
 * 
 * 	// @todo add external or app-wide custom inflections (for other languages)
 * 	// @todo add more rules for singularize and pluralize
 * 	
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 06.10.2008
 */
class Inflector extends Object {
	
	public static $instance;
	
	public function instance() {
		if (!self::$instance) {
			self::$instance = new Inflector();
		}
		return self::$instance;
	}
	
	/**
	 * 	Some rules are created on base of the wiki entry
	 * 	{@link http://en.wikipedia.org/wiki/English_plural}
	 *
	 * 	@param string $string
	 * 	@return string
	 */
	public static function pluralize($string) {
		if (!is_string($string)) return $string;
		if (empty($string)) return $string;
		$consonants = 'bcdfghjklmnpqrstvwxy';
		if (preg_match('@news@i', $string)) {
			return $string; 
		}
		$rules = array(
			// The -ies rule: nouns ending in a y preceded by a consonant usually drop the y and add -ies
			'/(.*['.$consonants.'])y$/' => '\\1ies',
			// The -oes rule: most nouns ending in o preceded by a consonant also form their plurals by adding -es
			'/(.*['.$consonants.'])o$/' => '\\1oes',
			'/(.*)h$/'  => '\\1es',
			'/(.*)s$/'	=> '\\1ses'
		);
		foreach($rules as $regexp => $replace) {
			$result = preg_replace($regexp, $replace, $string);
			if ($result !== $string) {
				return $result;
			}
		}
		return $string.'s';
	}
	
	/**
	 *	Shortcut for {@link pluralize}
	 * 	@param $string string
	 * 	@return string
	 */
	public static function plural($string) {
		return self::pluralize($string);
	}
	
	/**
	 * 	Tries to return the singular word of the $string
	 *
	 * 	@param string $string
	 * 	@return string
	 */
	public static function singularize($string) {
		if (!is_string($string)) return $string;
		$string = trim($string);
		if (empty($string)) return $string;
		$rules = array(
			'/(.*)ies$/i' => '\\1y'
		);
		foreach($rules as $regexp => $replace) {
			if (($result = preg_replace($regexp, $replace, $string)) !== $string) {
				return $result;
			}
		}
		return $string.'s';
	}
	
	public static function singular($string) {
		return self::singularize($string);
	}
	
	/**
	 *	camlize a string
	 * 	
	 * 	<a href="http://en.wikipedia.org/wiki/CamelCase">Camelcase</a> is a
	 * 	very common format for variable names in programming languages.
	 * 
	 * 	@param string $string
	 * 	@param boolean $upper uppercase the first character too?
	 * 	@return string
	 */
	public static function camellize($string, $upper = false) {
		$result = preg_replace('@\s+@', '', ucwords(preg_replace('@(_|\s)+@', ' ', $string)));
		if ($upper == false) {
			$result = lcfirst($result);
		}
		return $result;
	}
	
	/**
	 *	Delimeter seperate words just_as_this_is.
	 * 	<code>
	 * 	$example = 'Hello my Name is karl';
	 * 	// echoes 'Hello_my_Name_is_karl'
	 * 	echo String::delimeterSeperate($example);
	 * 	</code>
	 * 	@param string $string
	 * 	@param string $delimeter The delimeter ot use, usually its an underscore _
	 * 	@return string converted string
	 */
	public static function delimeterSeperate($string, $delimeter = '_', $lowered = true) {
		if ($delimeter == null) $delimeter = '_';
		$string = preg_replace('@(?!^)([A-Z]|\s+)@', $delimeter.'\\1', $string);
		$string = preg_replace('@('.$delimeter.'){2,}@', $delimeter, $string);
		if ($lowered) {
			$string = strtolower($string);
		}
		return $string;
	}
	
	/**
	 *	Alias for {@link delimeterSeperate}
	 * 	@param string $string
	 * 	@return string
	 */
	public static function underscore($string) {
		return self::delimeterSeperate($string, '_', true);
	}
	
	/**
	 *	Seperates string into modelname and fieldname and returns an array
	 *	<code>
	 *	// will return array('ModelName', 'fieldName');
	 *	var_dump(Inflector::modelAndFieldName('ModelName.fieldName');
	 *	</code>
	 *	@param string $string
	 *	@return array(string)
	 */
	public static function splitModelAndFieldName($string) {
		if (strpos($string, '.') !== false) {
			return explode('.', $string);
		}
		return array('', $string);
	}
	
}

?>