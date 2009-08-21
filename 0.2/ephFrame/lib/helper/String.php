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

// load classed needed
ephFrame::loadClass('ephFrame.lib.helper.Charset');

/**
 * Manipulating / Analyzing Strings
 * 
 * I tried to implement all kinds of methods that you might need for 
 * sanitizing, analyzing, counting, creating or just manipulating strings.
 * I also tried to keep an eye on multi-byte strings. Some methods are not
 * multibyte aware but the most are.
 * 
 * This class is tested by {@link TestString}
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 02.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 * @version 0.2.1
 * @uses Charset
 */
class String extends Helper {
	
	/**
	 * Convert a $string to an url conform string by replacing space with
	 * $spaceReplace Character and lowercase the string.
	 * Returns false if the resulting string is empty.
	 *
	 * <code>
	 * $test = 'This will be an uri soon';
	 * echo String::toURL($text, '_');
	 * </code>
	 *
	 * @param string $string
	 * @param string $spaceReplace string that should replace multiple/single space characters
	 * @param boolean $noCase transform string to lowercase
	 * @return string
	 */
	public static function toURL($string, $spaceReplace = '-', $noCase = true) {
		$string = trim($string);
		$string = strip_tags($string);
		$string = Charset::toASCII($string);
		$string = preg_replace('@\s+@', $spaceReplace, $string);
		$string = preg_replace('@([^a-zA-Z0-9_,.+-])@', '', $string);
		$string = trim($string, $spaceReplace);
		// replace single & multiple spaces
		if (strlen($spaceReplace) > 0) {
			$string = preg_replace('@'.preg_quote($spaceReplace, '@').'{2,}@', $spaceReplace, $string);
		}
		// return false if string result is empty
		if (strlen($string) == 0) {
			return false;
		}
		// lowercase string, no need for String helper cause it's only ascii
		if ($noCase) {
			$string = strtolower($string);
		}
		return $string;
	}
	
	/**
	 * Appends a string to an other.
	 * 
	 * Pass optional $condString that will checked before Adding, if $condString
	 * is allready appended to the string nothing will change.
	 *
	 * @param string $string
	 * @param string $append
	 * @param string $condString
	 * @param boolean $caseSensitive
	 * @param boolean $isEmpty
	 * @return string
	 */
	public static function append($string, $append, $condString = null, $caseSensitive = false, $isEmpty = true) {
		assert(is_scalar($string) && is_scalar($append));
		if ($condString !== null) {
			if ($condString == true) $condString = $append;
			if (($caseSensitive && substr($string, -strlen($condString)) == $condString) || 
			   (!$caseSensitive && self::lower(substr($string, -strlen($condString))) == String::lower($condString))
				) {
				return $string;
			}
		}
		return $string.$append;
	}
	
	/**
	 * Prepends a string to an other
	 *
	 * Prepends $prepend string to $string if optional $condString is not present
	 * at the beginning of the text in $caseSensitive. String is not prepended
	 * if input $string is empty and $isEmpty.
	 * 
	 * @param string $string
	 * @param string $prepend
	 * @param string $condString
	 * @param boolean $caseSensitive
	 * @param boolean $isEmpty prepends the string only if the input $string was not empty
	 * @return string
	 */
	public static function prepend($string, $prepend, $condString = null, $caseSensitive = false, $isEmpty = true) {
		$string = (string) $string;
		if ($condString !== null) {
			if ($condString == true) $condString = (string) $prepend;
			if (($caseSensitive && substr($string, 0, -strlen($condString)) == $condString) || 
			   (!$caseSensitive && self::lower(substr($string, 0, -strlen($condString))) == String::lower($condString))
				) {
				return $string;
			}
		}
		if (empty($string) && $isEmpty == false) return $string;
		return $prepend.$string;
	}
	
	/**
	 * Returns $i characters from the left of $string and returns it.
	 * 
	 * <code>
	 * // should echo 'Mähdrescher'
	 * echo String::left('Mähdrescher', 3);
	 * </code>
	 * 
	 * @param string $string
	 * @param integer $i number of characters to return
	 * @return string
	 */
	public static function left($string, $i = 0) {
		return self::substr($string, 0, $i);
	}
	
	/**
	 * Returns $i characters from the right of a/the string.
	 * 
	 * <code>
	 * // should echo 'drescher'
	 * echo String::right('Mähdrescher', 7);
	 * </code>
	 * 
	 * @param string $string
	 * @param integer $i number of characters to return
	 * @return string
	 */
	public static function right($string, $i = 0) {
		if ($i == 0) {
			return '';
		}
		return self::substr($string, -$i);
	}
	
	/**
	 * Lowers a string, utf8-save
	 * If you're not sure about the encoding of a string, try that one and
	 * you'll get a lowered string.
	 * 
	 * <code>
	 * // should echo 'MÄHDRESCHER'
	 * echo String::upper('Mähdrescher');
	 * </code>
	 * 
	 * @param string $string
	 * @param string $charset
	 * @param integer $start optional index of character to start uppercase conversion
	 * @param integer $end
	 * @return string
	 */
	public static function upper($string = null, $start = 0, $end = null, $charset = Charset::UTF_8) {
		$string = (string) $string;
		if (Charset::isUTF8($string)) {
			if ($end !== null) {
				return self::substr($string, 0, $start).mb_strtoupper(self::substr($string, $start, $end), $charset).self::substr($string, $end);
			}
			return mb_strtoupper($string, Charset::UTF_8);
		} else {
			if ($end !== null) {
				return self::substr($string, 0, $start).strtoupper(self::substr($string, $start, $end)).self::substr($string, $end);
			}
			return strtoupper($string);
		}
	}
	
	/**
	 * Uppers the first character of a string, multibyte safe, and returns it.
	 * 
	 * <code>
	 * $s = 'östlich';
	 * // returns 'Östlich'
	 * echo String::ucFirst($s);
	 * echo $s->ucFirst();
	 * </code>
	 *
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function ucFirst($string = null, $length = 1) {
		return self::upper($string, 0, $length);
	}
	
	/**
	 * Lowers all characters in a string, this method should be multibyte save. 
	 * @param string $string
	 * @param integer $start
	 * @param integer $end
	 * @param string $charset
	 * @return string
	 */
	public static function lower($string = null, $start = 0, $end = null, $charset = Charset::UTF_8) {
		$string = (string) $string;
		if (Charset::isUTF8($string)) {
			if ($end !== null) {
				return self::substr($string, 0, $start).mb_strtolower(self::substr($string, $start, $end), $charset).self::substr($string, $end);
			}
			return mb_strtolower($string, $charset);
		} else {
			if ($end !== null) {
				return self::substr($string, 0, $start).strtolower(self::substr($string, $start, $end)).self::substr($string, $end);
			}
			return strtolower($string);
		}
	}

	/**
	 * Uppers the first character of a string and returns it
	 *
	 * @param string $string
	 * @param integer $length
	 * @param string $charset optional charset encoding string
	 * @return string
	 */
	public static function lcFirst($string, $length = 1) {
		return self::lower($string, 0, $length);
	}
	
	/**
	 * Substitutes placeholders in a string with the values from the passed 
	 * array. You can use word-like placeholders and numbers for the arrays
	 * with numeric indexes or other calls.
	 * 
	 * See the examples that explain that much better: 
	 * <code>
	 * // should echo 'You see 1 from 100 results'
	 * echo String::substitute('You\'re seeing :page of :total pages.', array(
	 * 'total' => 100,
	 * 'page' => 1
	 * ));
	 * </code>
	 * 
	 * Skip the array parameter, and pass everything as arguments:
	 * <code>
	 * // should echo 'You see fred before charlene'
	 * echo String::substitute('You see :2 before :1', 'fred', 'charlene');
	 * </code>
	 * 
	 * This can also be called by passing just one argument that should be replaced:
	 * <code>
	 * echo String::substitute('Hello :1!', $username);
	 * </code>
	 * 
	 * @param string	$template Template with placeholders
	 * @param array(string)	$arr
	 * @return string
	 */
	public static function substitute($template, $arr = array()) {
		$template = (string) $template;
		// if empty template or no marks found
		if (empty($template) || !preg_match_all('/:([\p{L}0-9\.\-_]+)?/', $template, $found)) {
			return $template;
		}
		// multiple arguments caled
		if (func_num_args() > 2) {
			$args = func_get_args();
			$arr = array_slice($args, 1, count($args)-1, true);
		}
		if (!is_array($arr)) {
			$arr = array($arr);
		}
		$result = $template;
		foreach($found[0] as $index => $foundKey) {
			if (array_key_exists($found[1][$index], $arr)) {
				$result = str_replace($foundKey, $arr[$found[1][$index]], $result);
			}
		}
		return $result;
	}
	
	/**
	 * alias for {@link substitute}
	 * @param strign $template
	 * @param array(string) $arr
	 * @return string
	 */
	public static function replace($template, $arr = array()) {
		if (func_num_args() > 2) {
			$arr = func_get_args();
			$arr = array_slice($arr, 1, count($arr)-1, true);
		}
		return self::substitute($template, $arr);
	}
	
	/**
	 * Indent a string
	 * 
	 * Just like every advanced text editor this method indents every line of
	 * a string a bit. The main effort of this method is that i can indent
	 * multiline strings. PHP's native method str_pad can only work with one
	 * line strings.
	 * 
	 * @param string $in String that should be intended
	 * @param integer $n	Steps to intend
	 * @param string $char	Character to use to intend, multibytes accepted
	 * @param integer $skiplines Optional number of lines that should be skipped
	 * @return string	the indented string result
	 */
	public static function indent($in, $n = 1, $char = TAB, $skipLines = 0) {
		$lines = preg_split('/\n{1}/', $in);
		$numberOfLines = self::numberOfLines($in);
		foreach ($lines as $index => $line) {
			if ($index >= $skipLines) $lines[$index] = str_repeat($char, $n).$line;
		}
		return implode(LF, $lines);
	}
	
	/**
	 * Wrapper and route for counting the characters in a string the 
	 * multibyte way, this should be extended any time the php internal
	 * stuff changes
	 * @param string $string
	 * @return integer
	 * @static
	 */
	public static function length($string) {
		if (Charset::isUTF8($string)) {
			return mb_strlen($string, 'UTF-8');
		} else {
			return strlen($string);
		}
	}
	
	/**
	 * Extracts a part of a string and returns it.
	 * 
	 * <code>
	 * // should echo 'Mäh'
	 * echo String::substr('Mähdrescher', 0, 3);
	 * </code>
	 * 
	 * @param string $string
	 * @param integer $start
	 * @param integer $length
	 */
	public static function substr($string, $start = null, $length = null) {
		$string = (string) $string;
		if ($length === 0) {
			return '';
		}
		if (Charset::isUTF8($string)) {
			if ($length == null) {
				if ($start < 0) {
					$length = abs($start);					
					$start = self::length($string) - $length;
				} elseif ($start > 0) {
					$length = self::length($string) - $start;
				} elseif ($start == 0) {
					$length = self::length($string);
				}
			}
			return mb_substr($string, $start, $length, 'UTF-8');
		} else {
			if ($length == null) {
				return substr($string, $start);
			}
			return substr($string, $start, $length);
		}
	}
	
	/**
	 * Counts all words in a text and returns the words and their count
	 * as indexed array. This will cut every html code and ignores it.
	 * <code>
	 * $text = 'I\'m a text that has some words!';
	 * var_dump(String::countWords($text, 3));
	 * // ['I\'m'] = 1, ['text'] = 1, ['that'] = 1 ... 
	 * </code>
	 * // todo this does not seem to work like the docu sais!? wtf?
	 * @param string $input
	 * @param integer $minLenght Minimum length of words to be counted
	 * @return array(mixed)
	 */
	public static function countWords($input, $minLength = null) {
		if (!is_string($input)) throw new StringExpectedException();
		$string = self::stripTags($input);
		if ($minLength === null) {
			$regexp = '/([\p{L}\p{N}]+-?(\p{L}\p{N}+)?)/';
		} else {
			$regexp = '/([\p{L}\p{N}]{'.$minLength.',}-?(\p{L}\p{N}+)?)/';
		}
		$foundWords = array();
		$foundKeywordsNum = preg_match_all($regexp, $string, $foundWords);
		if (!isset($foundWords[0])) {
			return $foundWords;
		}
		return $foundWords;
	}
	
	/**
	 * Counts the sentences in a string counting the european language sentence
	 * endings like ?.! ...
	 * // todo finish this
	 * @param string $string
	 * @return integer
	 */
	public static function countSentences($string) {
		
	}
	
	/**
	 * Counts the number of paragraphs in a text, just counting line breaks
	 * is the simpler explaination
	 * @param string $string
	 * @return integer Number of paragraphs found
	 */
	public static function countParagraphs($string) {
		if (!is_string($string)) return null;
		return substr_count($string, LF);
	}
	
	/**
	 * Truncates a string if it's to long and optionally adds something 
	 * ad the end.
	 * 
	 * <code>
	 * // shows 'hello my name...'
	 * echo String::truncate('hello my name is oscar wilde', 14, '…');
	 * // if you have the probabily of a string with no space in it
	 * // you can use the force parameter
	 * echo String::truncate('LongUsernameIsHard', 10, '…');
	 * // prints 'LongUse...' 
	 * </code>
	 * 
	 * @param string	$string
	 * @param integer	$length
	 * @param string	$end
	 * @param boolean	$force
	 */
	public static function truncate($string, $length, $end = '', $force = false, $calculateWidths = true) {
		$strLength = self::length($string);
		if ($strLength <= $length) return $string;
		$endStrLength = self::length($end);
		if ($endStrLength == 0) {
			$lengthToCut = $length;
		} else {
			$lengthToCut = $length - $endStrLength;
		}
		$truncated = '';
		$lastSpace = 0;
		for ($i = 0; $i < $strLength; $i++) {
			$char = self::substr($string, $i, 1);
			if ($calculateWidths) {
				$lengthToCut += self::charWidth($char);
			}
			if (preg_match('/\s/', $char)) {
				$lastSpace = $i;
			}
			if ($i >= $lengthToCut) {
				break;
			}
		}
		if ($lastSpace == 0 || $force) {
			$truncated = mb_substr($string, 0, $lengthToCut, 'UTF-8');
		} else {
			$truncated = mb_substr($string, 0, $lastSpace, 'UTF-8');	
		}
		return $truncated.$end;
	}
	
	/**
	 * Adds line brakes to strings that have very long words in it. This
	 * will add line brakes to very long words or phrases with no spaces
	 * between that are longer than $maxLineLength.
	 * 
	 * This is very helpfull to prefent the page layout from exctracting
	 * by very long links. Just think about the 100px column that should
	 * hold a user-edited text!
	 * 
	 * <code>
	 * $example = 'Some user-strings have very long woooooooooooooooooooords in it and will the layer they're in.';
	 * echo String::wrap($example, 20);
	 * </code>
	 * 
	 * @param string $string	
	 * @param integer $length maximum length of not-line-braked strings
	 * @param boolean $calculateWidths dynamic calculate character widths
	 * @return string
	 */
	public static function wrap($string, $maxLineLength, $calculateWidths = true) {
		$strLength = self::length($string); 
		if ($strLength <= $maxLineLength) return $string;
		$intag = false;
		$charsSinceLastBreak = 0;
		$wrapped = '';
		$cutPosition = $maxLineLength;
		for ($i = 0; $i < $strLength; $i++) {
			$char = self::substr($string, $i, 1);
			$wrapped .= $char;
			if ($char == '<') {
				$intag = true;
			} elseif ($char == '>') {
				$intag = false;
			} elseif (!$intag) {
				if ($calculateWidths) {
					$cutPosition += self::charWidth($char);
				}
				if (preg_match('/\s/', $char)) {
					$charsSinceLastBreak = 0;
					$cutPosition = $maxLineLength;
				} elseif ($charsSinceLastBreak >= $cutPosition) {
					$charsSinceLastBreak = 0;
					$wrapped .= LF;
					$cutPosition = $maxLineLength;
				}
				$charsSinceLastBreak++;
			}
		}
		return $wrapped;
	}
	
	/**
	 * Characters have different width. some are very broad, some a thin.
	 * This method tries to return an aproximate with value for a character.
	 * @param string $char
	 * @return integer
	 */
	public static function charWidth($char) {
		// super wide characters
		if (preg_match('/[©@®@—]/', $char)) {
			return -1.2;
		// wide characters
		} elseif (preg_match('/[WQTZMDGOÖÓÒÔHUÜÚÙÛÄÁÀÂ]/i', $char)) {
			return -0.8;
		// thin characters
		} elseif (preg_match('/[ilt1j\.,;:´`!\(\)\'*()\|\[\]]/', $char)) {
			return +0.3;
		// all others
		} else {
			return 0;
		}
	}
	
	/**
	 * Salts a string.
	 * This is very usefull for md5 hashed password strings (rainbow table
	 * protection.)
	 * <code>
	 * $in = 'Ephigenia';
	 * $salt = 'abcdefghij';
	 * // result is: Eßpähyiäg.e,n^i#a
	 * echo String::salt($in, $salt);
	 * // result is: Eßäpyähigenia
	 * echo String::salt($in, $salt, 2);
	 * </code>
	 * 
	 * @param string $string
	 * @param string $string
	 * @param integer $width with of salt characters inserted
	 * @return string
	 * // todo this method also needs to have a checkup
	 */
	public static function salt($string, $salt, $width = 1) {
		$stringLength = self::length($string);
		$saltLength = self::length($salt);
		if ($stringLength == 0) {
			return $salt;
		} elseif ($stringLength == 1) {
			return $string.$salt;
		}
		// enhance salt with salt if to short
		if ($saltLength < $stringLength) {
			$salt = str_repeat($salt, ceil($stringLength / $saltLength) * $saltLength * $width);
		}
		$saltedString = '';
		for($i = 0; $i < $stringLength; $i++) {
			$saltedString .= self::substr($string, $i, 1);
			$saltedString .= self::substr($salt, $i, $width);
		}
		return $saltedString;
	}
	
	/**
	 * generates a random string of $length length
	 * <code>
	 * 	// create random password
	 * $str_passwort = String::createRandomString(6);
	 * 	// create password just with letter and some special chars
	 * 	$str_password = String::createRandomString(8, 'A-Z');
	 * </code>
	 *
	 * @param	integer	$length length of the string
	 * @param 	string	$string Salt, Characters the password is created from
	 * @return	string	generated string
	 **/
	public static function randomString($length = 8, $salt = null) {
		$length = (int) abs($length);
		if (empty($salt)) $salt = 'A-Za-z0-9';
		// custom salt with patterns
		if ($salt % 3 == 0) {
			$salt = strtr($salt, array(
					'a-z' => 'abcdefghijklmnopqrstuvwxyz',
					'A-Z' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
					'0-9' => '0123456789',
					'1-9' => '123456789'
				));
		}
		// create random string
		srand((double) microtime() * 1000000); // start the random generator
		$string = '';
		for ($i = 0; $i < $length; $i++) {
			$string .= substr($salt, rand() % strlen($salt), 1);
		}
		return $string;
	}
	
	/**
	 * Alias for {@link randomString}
	 * @return string
	 */
	public static function random($length = 8, $salt = null) {
		return self::randomString($length, $salt);
	}
	
	/**
	 * Creates a Password using {@link randomString} or you
	 * pass 'human' or 'humanreadable' as $salt and then {@link $genereateHumanReadablePassword} is used
	 * 
	 * @param integer	$length length of password
	 * @param string	$salt	Salt String, linke {@link randomString} or 'human'
	 * @return string
	 */
	public static function generatePassword($length, $salt = null) {
		if (in_array($salt, array('human', 'humanreadable', 'readable'))) {
			return self::generateHumanReadablePassword();
		} else {
			return self::randomString($length, $salt);
		}
	}
	
	/**
	 * Generates a password that is readable by humans due to vocal
	 * consonant patterns. Please consider that these passwords are not
	 * very save and can be cracked easily by brute fore attacks
	 * 
	 * This method is from the 'PHP Sicherheit' Book, published by dpunkt
	 * 
	 * @param integer	$length
	 * @return string
	 */
	public static function generateHumanReadablePassword($length = 8) {
		assert(is_int($length) && !empty($length));
		$vocals = array('a', 'e', 'i', 'o', 'u', 'ae', 'ou', 'io', 'ea', 'ou', 'ia', 'ai');
		$consonants = array('b', 'c', 'd', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p',
							'r', 's', 't', 'u', 'v', 'w', 'tr', 'cr', 'fr', 'dr',
							'wr', 'pr', 'th', 'ch', 'ph', 'st', 'sl', 'cl');
		$vCount = count($vocals);
		$cCount = count($consonants);
		$password = '';
		for ($i = 0; $i < $length; $i++) {
			$password .= $consonants[rand(0, $cCount-1)].$vocals[rand(0, $vCount-1)];
		}
		return substr($password, 0, $length);
	}
	
	/**
	 * Returns the number of lines in a string (indicated by LF) as integer
	 * @param string $string
	 * @return integer
	 */
	public static function countLines($string) {
		return substr_count($string, LF);
	}
	
	/**
	 * Returns the number of lines (indicated by \n) in a string
	 * @param string $string
	 * @return integer
	 */
	public static function numberOfLines($string) {
		assert(is_string($string));
		preg_match_all('/\\n/', $string, $found);
		if (empty($found[0])) return 0;
		return count($found[0]);
	}
	
	/**
	 * Returns an array out of string by splitting it into every line found.
	 * @param string $string
	 * @param boolean $ignoreEmpty ignore empty lines, 2 strips also totally empty lines
	 * @return array(string)
	 */
	public static function eachLine($string, $ignoreEmpty = false) {
		assert(is_scalar($string));
		$result = preg_split('/\n/', $string);
		if (!$ignoreEmpty) {
			return $result;
		}
		$strippedResult = array();
		foreach ($result as $line) {
			if ($ignoreEmpty == 2) {
				$line = trim($line);
			}
			if (!empty($line)) {
				$strippedResult[] = $line;
			}
		}
		return $strippedResult;
	}
	
	/**
	 * Adds Line Numbers to a string
	 * @param string $in
	 * @param integer $start Starting line number
	 * @param string $padString optional string that is added in front of lower numbers for spacing
	 * @return string
	 */
	public static function addLineNumbers($in, $start = 1, $padString = '0') {
		$lines = preg_split('/\n{1}/', $in);
		$numberOfLines = self::numberOfLines($in);
		$padLength = strlen($numberOfLines); // no need for unicode, cause length is an integer
		foreach ($lines as $index => $line) {
			$lines[$index] = str_pad($index+$start, $padLength, $padString, STR_PAD_LEFT).' '.$line;
		}
		return implode(LF, $lines);
	}
	
	/**
	 * Converts all bytes in a string to their hex value and returns
	 * the string. You can add a spacer to add some kind of column 
	 * structure to the returned string
	 * @param string	$string	String to be as hex
	 * @param string	$spacer spacer string
	 * @param integer	$breakAfterBytes number of bytes when a breaks comes
	 * @return string
	 */
	public static function hex($string, $spacer = '', $breakAfterBytes = 0) {
		$strlen = self::length($string);
		$return = '';
		for ($i = 0; $i < $strlen; $i++) {
			// create hex equivalent of string value and padd it with 0 at beginning
			$hex = str_pad(sprintf('%X', ord($string[$i])), 2, '0', STR_PAD_LEFT);
			if ($breakAfterBytes == 0 ||
				$breakAfterBytes > 0 && (($i-1) % $breakAfterBytes) == 1) {
				$hex .= $spacer;
			}
			$return .= $hex;
			if ($breakAfterBytes > 0 && ($i % $breakAfterBytes == 1)) {
				$return .= LF;
			}
		}
		return $return;
	}
	
	/**
	 * Convert a string to base36
	 * {@link http://en.wikipedia.org/wiki/Base_36}
	 * @param string
	 * @return string
	 */
	public static function toBase36($string) {
		return base_convert($string, 10, 36);
	}
	
	/**
	 * Convert a string back form base36
	 * @param $string
	 * @return string
	 */
	public static function fromBase36($string) {
		return base_convert($string, 36, 10);
	}
	
	/**
	 * Encodes every character in the string to HTML Encoded characters,
	 * this is multibyte save
	 * @param string $string String to encode
	 * @see htmlOrdDecode
	 * @return string
	 */
	public static function htmlOrdEncode($string) {
		$encodedString = '';
		if (Charset::isUTF8($string)) {
			$len = mb_strlen($string, 'UTF-8');
			for ($i = 0; $i < $len; $i++) {
				$char = mb_substr($string, $i, 1, 'UTF-8');
				$encodedString .= '&#'.self::ord($char).';';
			}
		} else {
			$len = strlen($string);
			for ($i = 0; $i < $len; $i++) {
				$char = substr($string, $i, 1);
				$encodedString .= '&#'.self::ord($char).';';
			}
		}
		return $encodedString;
	}
	
	/**
	 * Returns the character code of a character just like the php native
	 * method ord, but this method can handle unicode characters
	 * 
	 * This litte exmple shows you what makes this method usefull:
	 * <code>
	 * $char = 'é';
	 * // will echo just the first byte ord: 195
	 * echo ord($char)
	 * // but we'll need the correct unicode ord which is 233
	 * echo String::ord($char);
	 * </code>
	 *
	 * @param string $c
	 * @return integer
	 */
	public static function ord($c) {
		$h = ord($c{0});
		if ($h <= 0x7F) {
			return $h;
		} else if ($h < 0xC2) {
			return false;
		} else if ($h <= 0xDF) {
			return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
		} else if ($h <= 0xEF) {
			return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6
						| (ord($c{2}) & 0x3F);
		} else if ($h <= 0xF4) {
			return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12
						| (ord($c{2}) & 0x3F) << 6
						| (ord($c{3}) & 0x3F);
		} else {
			return false;
		}
	}
	
	/**
	 * Returns string crypted like the passwords in .htpasswd files
	 * (not multibyte aware and not well tested on different apache versions!)
	 * @param string	$string
	 * @return string
	 */
	public static function htpasswdencode($string) {
		return crypt($string, substr($string, 0, 2));
	}
	
	const REGEXP_CONTROL_CHARS = '![\x00-\x1F\7F]!u';
	const REGEXP_CONTROL_CHARS_WITH_BRAKES = '![\p{C}][\x0A\x0D]!u';
	
	/**
	 * Checks string for ascii control characters, such as
	 * device 1, 0 string ...
	 * @param string $input
	 * @return boolean
	 */
	public static function hasControlChars($input) {
		return preg_match(self::REGEXP_CONTROL_CHARS, $input);		
	}
	
	/**
	 * Strips any control charactes from a string
	 * @param string	$input
	 * @return string
	 */
	public static function stripControlChars($input) {
		return preg_replace(self::REGEXP_CONTROL_CHARS, '', $input);
	}
	
	const REGEXP_HTML_TAGS = '/(<|%3C|&lt;?|&gt;?&#0*60;?|&#0*62;?|&#x0*3c;?|&#x0*3e;?|\\\x3c|\\\x3e|\\\u003c|\\\u003e)*/i';
	
	/**
	 * Checks the strings for any tags
	 * @param string $string
	 * @return boolean
	 */
	public static function hasTags($string) {
		$return = preg_match(self::REGEXP_HTML_TAGS, $return);
	}
	
	/**
	 * Strips every kind of opening or closing html tag from the given
	 * string. Also strippes the possible encodings of < and >
	 * @param string $string
	 * @return string
	 */
	public static function stripTags($string) {
		return preg_replace(self::REGEXP_HTML_TAGS, '', $string);
	}
	
	/**
	 * Removes all PHP/Javascript style comments from a string
	 * comments with #, *, /*
	 * @param string	$string
	 * @return string
	 */
	public static function stripComments($string) {
		return preg_replace('{
				# multiline comments
				(?<!")					# dont match comments in strings
				/\*+((.|\s)*?)\*\/\n?
				# or	
				|
				# single line commments
				(^|\s+)\/{2}(.*)
				}ix', '', $string);
	}
	
	/**
	 * Strips all breaks (html and ascii ones) from a string
	 * @param string	$string
	 * @param string	$replace	Replace with this string
	 */
	public static function stripBrakes($string, $replace = '') {
		return preg_replace('/([\\r|\\n|\0|\x0B]|\\<br\\>|\\<br \\/\\>|\\<p\\>|\\<p \\/\\>)/', $replace, $string);
	}
	
	/**
	 * Tests wheter string has any line breaks in it
	 * @param string	$string
	 * @return boolean
	 */
	public static function hasBrakes($string) {
		return (preg_match('@(\r|\n)+@', $string));
	}
	
	/**
	 * Stripping multiple white space characters, to prevent inputs like this:
	 * <code>
	 * $name = 'Hanz         Meiser';
	 * // will print 'Hans Meiser'
	 * </code>
	 * @param string $string
	 * @return string
	 */
	public static function stripMultipleWhiteSpace($target) {
		return preg_replace('/([\s]+){1,}/', '$1', $target);
	}
	
	/**
	 * Strip space from every line in a string
	 * <code>
	 * $test = "     Foobar is funny\n but tralala     \n sing along"
	 * // will echo "Foobar is funny\nbut tralala\nsing along"
	 * echo String::
	 * </code>
	 * @param string $string
	 * @return string
	 */
	public static function trimEveryLine($string) {
		$tmp = explode(LF, $string);
		// cleaning string from spaces and other stuff like \n \r \t
		for ($a = 0, $c = count($tmp); $a < $c; $a++) $tmp[$a] = trim($tmp[$a]);
		return join('', $tmp);
	}
	
	/**
	 * Normalizing the brakes in a string to UNIX Brakes
	 * they are displayable on Mac, Linux and PC. All Line Brakes are
	 * converted to UNIX line brakes - \n
	 * @param string	$string
	 * @return string
	 */
	public static function normalizeBrakes($string) {
		return preg_replace('!(\r\n|\r)!', LF, $string);
	}
	
	/**
	 * Removes all non-alpha numerical charachters (not unicode aware)
	 * @param string	$string
	 * @return string
	 */
	public static function stripNonAlphaNumerical($string) {
		return preg_replace('/[^\w]/', '', $string);
	}
	
	/**
	 * Removes all alpha numerical charachters (not unicode aware)
	 * @param string	$string
	 * @return string
	 */
	public static function stripAlphaNumerical($string) {
		return preg_replace('/([^\d)/', '', $string);
	}
	
	/**
	 * Removes the repition of the same characters in a string just like
	 * the ruby method would do.
	 * @param string
	 * @return string
	 */
	public static function squeeze($string) {
		return preg_replace('/(\p{L}|\s){1}(\1{1,})/i', '$1', $string);	
	}
	
	/**
	 * Returns an array of lines in the string
	 * @return array(string)
	 * @param string
	 */
	public static function splitLines($string) {
		return preg_split('/[\r\n]+/', $string);
	}
	
	/**
	 * Swaps the case of letters in a string from low to high and high to low
	 * //TODO finish swapCase method
	 * @param string $string
	 * @return string
	 */
	public static function swapCase($string) {
		return 'String::swapCase not finished yet.';
	}
	
	/**
	 * Capitalizes every word in a string
	 * @param string $string
	 * @return string
	 */
	public static function capitalize($string) {
		return ucwords($string);
	}
	
	/**
	 * Returns an array of characters in a string. This method is utf8 save
	 * and won't split the entities into seperate array elements.
	 * 
	 * <code>
	 * // split string into array, should echo 'Ä,B,Ö'
	 * echo implode(',',String::each('ÄBÖ')); 
	 * </code>
	 * 
	 * @param string
	 * @return array(string)
	 */
	public static function each($string) {
		assert(is_scalar($string));
		$splitted = array();
		$multibyte = '';
		for($i = 0; $i < strlen($string); $i++) {
			$c = $string{$i};
			$n = ord($c);
            if (($n &0x80) == 0) {
                // a 1-byte UTF-8 character
                $multibyte and $splitted[] = $multibyte and $multibyte = '';
                $splitted[] = $c;
            } else if (($n &0xC0) == 0x80) {
                // a following byte
                $multibyte .= $c;
            } else {
                // the first byte of a muti-byte UTF-8 character
                $multibyte and $splitted[] = $multibyte;
                $multibyte = $c;
            }
        }
        $multibyte and $splitted[] = $multibyte;
        return $splitted;
	}
	
	/**
	 * Insert $replaceWith into $string at $position:
	 * <code>
	 * // should echo 'ABCDEFG'
	 * echo String::insert('ADEFG', -4, 'BC');
	 * </code>
	 * 
	 * @param string $string
	 * @param integer $position
	 * @param string $replaceWith
	 * @return string
	 */
	public static function insert($string, $position, $replaceWith = '') {
		assert(is_scalar($string) && is_scalar($position) && is_scalar($replaceWith));
		return self::substr($string, 0, $position).$replaceWith.self::substr($string, $position);
	}
	
}
