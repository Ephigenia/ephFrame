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
 * String/Class Inflector class
 * 
 * This class singularizes, pluralizes from Strings to Classnames and from
 * Classnames to strings. Used a lot in the model classes.
 * 
 * // @todo add external or app-wide custom inflections (for other languages)
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.util
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 06.10.2008
 */
class Inflector
{
	private static $instance;
	
	public function instance() 
	{
		if (!self::$instance) {
			self::$instance = new Inflector();
		}
		return self::$instance;
	}
	
	/**
	 * Some rules are created on base of the wiki entry
	 * {@link http://en.wikipedia.org/wiki/English_plural}
	 *
	 * @param string $string
	 * @return string
	 */
	public static function pluralize($string)
	{
		if (!is_string($string)) return $string;
		if (empty($string)) return $string;
		$consonants = 'bcdfghjklmnpqrstvwxy';
		if (preg_match('@news@i', $string)) {
			return $string; 
		}
		// english language rules
		$rules = array(
			// The -ies rule: nouns ending in a y preceded by a consonant usually drop the y and add -ies
			'@(['.$consonants.'])y$@i' => '\\1ies',
			// The -oes rule: most nouns ending in o preceded by a consonant also form their plurals by adding -es
			'@(['.$consonants.'])o$@i' => '\\1oes',
			// other rules
			'@s$@i' => '\\1ses',
			// kiss, dish, witch
			'@(ss|sh|ch)$@' => '\\1es',
			// phase,
			'@(se)$@i' => 'ses',
			// judge, massage
			'@(dge|ge)$@i' => '\\1s',
			// calf -> calves
			'@fs$@i' => '\\1ves',
			// knife -> knifes
			'@ife$@i' => '\\1ives',
		);
		foreach($rules as $regexp => $replace) {
			if (($plural = preg_replace($regexp, $replace, $string)) != $string) {
				return $plural;
			}
		}
		return $string.'s';
	}
	
	/**
	 * Tries to return the singular word of the $string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function singularize($string)
	{
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
	
	/**
	 * Convert a string to so called CamelCase notation.
	 * 
	 * <a href="http://en.wikipedia.org/wiki/CamelCase">Camelcase</a> is a
	 * very common format for variable names in programming languages.
	 * 
	 * @deprecated use {@link camelize}
	 * @param string $string
	 * @param boolean $upper uppercase the first character too?
	 * @return string
	 */
	public static function camelize($string, $upper = false)
	{
		$result = preg_replace('@\s+@', '', ucwords(preg_replace('@(_|\s)+@', ' ', $string)));
		if ($upper == false) {
			$result = lcfirst($result);
		}
		return $result;
	}
		
	/**
	 * Delimeter seperate words just_as_this_is.
	 * 
	 * <code>
	 * $example = 'Hello my Name is karl';
	 * // echoes 'Hello_my_Name_is_karl'
	 * echo String::delimeterSeperate($example);
	 * </code>
	 * 
	 * @param string $string
	 * @param string $delimeter The delimeter ot use, usually its an underscore _
	 * @return string converted string
	 */
	public static function underscore($string, $seperator = '_')
	{
		$string = preg_replace('@(?!^)(([A-Z])|\s)@', $seperator.'\\2', trim($string));
		$string = preg_replace('@('.$seperator.'){2,}@', $seperator, $string);
		$string = strtolower($string);
		return $string;
	}
	
	/**
	 * Seperates string into modelname and fieldname and returns an array
	 * 
	 * <code>
	 * // will return array('ModelName', 'fieldName');
	 * var_dump(Inflector::modelAndFieldName('ModelName.fieldName');
	 * </code>
	 * 
	 * @param string $string
	 * @return array(string)
	 */
	public static function splitModelAndFieldName($string)
	{
		if (strpos($string, '.') !== false) {
			return explode('.', $string);
		}
		return array('', $string);
	}
}