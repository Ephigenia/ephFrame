<?php

namespace ephFrame\util;

class Inflector
{
	protected static $pluralizeRules = array(
		// The -ies rule: nouns ending in a y preceded by a consonant usually drop the y and add -ies
		'@(.+[bcdfghjklmnpqrstvwxy])y$@i' => '\\1ies',
		// The -oes rule: most nouns ending in o preceded by a consonant also form their plurals by adding -es
		'@(.+[bcdfghjklmnpqrstvwxy])o$@i' => '\\1oes',
		// -s
		'@status$@i' => '\\0',
		// -ss (kiss)
		'@.+ss$@' => '\\0es',
		'@(.+s)$@i' => '\\1ses',
		// -ss (dish, witch)
		'@(.+(sh|ch))$@' => '\\1es',
		// -se (phase),
		'@se$@i' => 'ses',
		// -dge|ge (judge)
		'@(?:dge|ge)$@i' => '\\1s',
		// calf -> calves
		'@f$@i' => '\\1ves',
		// life -> lives
		'@ife$@i' => '\\1ives',
	);
	
	/**
	 * Some rules are created on base of the wiki entry
	 * {@link http://en.wikipedia.org/wiki/English_plural}
	 *
	 * @param string $string
	 * @return string
	 */
	public static function pluralize($singular)
	{
		if (strncasecmp($singular, 'news', 4) == 0) {
			return $singular;
		}
		foreach (self::$pluralizeRules as $regexp => $replace) {
			if (preg_match($regexp, $singular)) return preg_replace($regexp, $replace, $singular);
		}
		return $singular.'s';
	}
	
	protected static $singularizeRules = array(
		'@ies$@i' => '\\1y',
		'@oes$@i' => '\\1o',
		'@sses$@' => '\\1ss',
		'@es$@i' => '\\1e',
		'@(.+)s$@i' => '\\1',
	);
	
	/**
	 * Tries to return the singular word of the $string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function singularize($plural)
	{
		foreach(self::$singularizeRules as $regexp => $replace) {
			if (preg_match($regexp, $plural)) return preg_replace($regexp, $replace, $plural);
		}
		return $plural;
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
}