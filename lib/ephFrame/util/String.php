<?php

namespace ephFrame\util;

use ephFrame\util\Charset;

/**
 * Manipulating of Strings
 * 
 * I tried to implement all kinds of methods that you might need for 
 * sanitizing, analyzing, counting, creating or just manipulating strings.
 * I also tried to keep an eye on multi-byte strings. Some methods are not
 * multibyte aware but the most are.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 02.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 * @version 0.2.2
 */
class String
{
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
	public static function left($string, $i = 0)
	{
		if ($i <= 0) return '';
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
	public static function right($string, $i = 0)
	{
		if ($i <= 0) {
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
	 * @return string
	 */
	public static function upper($string)
	{
		$string = (string) $string;
		if (strlen($string) == 0) return $string;
		if (Charset::isUTF8($string)) {
			return mb_strtoupper($string, Charset::UTF_8);
		} else {
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
	public static function ucFirst($string, $length = 1)
	{
		return self::upper(self::substr($string, 0, $length)).self::substr($string, $length);
	}
	
	/**
	 * Lowers all characters in a string, this method should be multibyte save. 
	 * @param string $string
	 * @param string $charset
	 * @return string
	 */
	public static function lower($string)
	{
		$string = (string) $string;
		if (strlen($string) == 0) return $string;
		if (Charset::isUTF8($string)) {
			return mb_strtolower($string, Charset::UTF_8);
		} else {
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
	public static function lcFirst($string, $length = 1)
	{
		return self::lower(self::substr($string, 0, $length)).self::substr($string, $length);
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
	public static function substitute($template, $arr = array())
	{
		$template = (string) $template;
		// if empty template or no marks found
		if (empty($template) || !preg_match_all('/:([a-z0-9\-_]+)?/i', $template, $found)) {
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
		uksort($arr, function($a, $b) {
			if (strlen($a) == strlen($b)) return 0;
			if (strlen($a) > strlen($b)) return -1;
			return 1;
		});
		foreach($arr as $key => $value) {
			$result = preg_replace('@:'.preg_quote($key, '@').'@', $value, $result);
		}
		return $result;
	}
	
	/**
	 * Indent a string
	 * 
	 * Just like every advanced text editor this method indents every line of
	 * a string a bit. The main effort of this method is that i can indent
	 * multiline strings. PHP's native method str_pad can only work with one
	 * line strings.
	 * 
	 * @param string $string
	 * @param integer $n	Steps to intend
	 * @param string $char	Character to use to intend, multibytes accepted
	 * @param integer $skiplines Optional number of lines that should be skipped
	 * @return string
	 */
	public static function indent($string, $n = 1, $char = "\t", $skipLines = 0)
	{
		$lines = preg_split('/\n/', $string);
		foreach ($lines as $index => $line) {
			if ($index < $skipLines) continue;
			$lines[$index] = str_repeat((string) $char, (int) $n).$line;
		}
		return implode(PHP_EOL, $lines);
	}
	
	/**
	 * Wrapper and route for counting the characters in a string the 
	 * multibyte way, this should be extended any time the php internal
	 * stuff changes
	 * @param string $string
	 * @return integer
	 * @static
	 */
	public static function length($string)
	{
		if (Charset::isUTF8($string)) {
			return mb_strlen($string, Charset::UTF_8);
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
	public static function substr($string, $start = null, $length = null)
	{
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
			return mb_substr($string, $start, $length, Charset::UTF_8);
		} else {
			if ($length == null) {
				return substr($string, $start);
			}
			return substr($string, $start, $length);
		}
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
	 * @todo make this ignore html tags
	 * @param string	$string
	 * @param integer	$length
	 * @param string	$end
	 * @param boolean	$force
	 */
	public static function truncate($string, $targetLength, $end = '', $force = false)
	{
		$strLength = self::length($string);
		if ($strLength <= $targetLength) return $string;
		$targetLength -= self::length(strip_tags($end));
		$truncatePos = 0;
		$inTag = false;
		for ($i = 0; $i < $strLength; $i++) {
			$char = self::substr($string, $i, 1);
			if ($truncatePos >= $targetLength) {
				break;
			} elseif ($char == '<') {
				$lastSpace = $i;
				$inTag = true;
			} elseif ($char == '>') {
				$inTag = false;
			} elseif (!$inTag) {
				if (preg_match('/\s/', $char)) {
					$lastSpace = $i;
				}
				$truncatePos += 1;
			}
		}
		if (empty($lastSpace) || $force) {
			$truncated = self::substr($string, 0, $truncatePos);
		} else {
			$truncated = self::substr($string, 0, $lastSpace);
		}
		return self::closeTags($truncated).$end;
	}
	
	/**
	 * Closes all opened tags in the string
	 * taken from here http://milianw.de/code-snippets/close-html-tags
	 * 
	 * It does not take care of the order of the tags!
	 * 
	 * @param string $html
	 * @return string
	 * @author Milian Wolff <mail@milianw.de> 
	 */
	public static function closeTags($html)
	{
		// put all opened tags into an array
		preg_match_all('@<([a-z]+)(?: .*)?(?<![/|/ ])>@iU', $html, $result);
		$openedtags = $result[1];
		$len_opened = count($openedtags);
		
		// put all closed tags into an array
		preg_match_all('@</([a-z]+)>@iU', $html, $result);
		$closedtags = $result[1];
		
		// all tags are closed
		if (count($closedtags) == $len_opened) {
			return $html;
		}
		
		$openedtags = array_reverse($openedtags);
		// close tags
		for ($i = 0; $i < $len_opened; $i++) {
			if (!in_array($openedtags[$i], $closedtags)){
				$html .= '</'.$openedtags[$i].'>';
			} else {
				unset($closedtags[array_search($openedtags[$i], $closedtags)]);
			}
		}
		return $html;
	}
	
	/**
	 * Adds line breakes to strings that have very long words in it. This
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
	 * @param integer $length maximum length of not-line-breaked strings
	 * @param boolean $calculateWidths dynamic calculate character widths
	 * @return string
	 */
	public static function wrap($string, $maxLineLength = 80)
	{
		$strLength = self::length($string); 
		if ($strLength <= $maxLineLength) return $string;
		$intag = false;
		$charsSinceLastBreak = 0;
		$wrapped = '';
		$cutPosition = $maxLineLength;
		for ($i = 0; $i < $strLength; $i++) {
			$char = self::substr($string, $i, 1);
			if ($char == '<') {
				$intag = true;
			} elseif ($char == '>') {
				$intag = false;
			} elseif (!$intag) {
				if (preg_match('/\s/', $char)) {
					$charsSinceLastBreak = -1;
					$cutPosition = $maxLineLength;
				} elseif ($charsSinceLastBreak >= $cutPosition) {
					$charsSinceLastBreak = 0;
					$wrapped .= PHP_EOL;
					$cutPosition = $maxLineLength;
				}
				$charsSinceLastBreak++;
			}
			$wrapped .= $char;
		}
		return $wrapped;
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
	public static function salt($string, $salt, $width = 1)
	{
		$stringLength = self::length($string);
		$saltLength = self::length($salt);
		if ($stringLength <= 1) {
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
	public static function random($length = 8, $salt = null)
	{
		$length = (int) abs($length);
		if (empty($salt)) $salt = 'A-Za-z0-9';
		// custom salt with patterns
		if (strlen($salt) % 3 == 0) {
			$salt = strtr($salt, array(
				'a-z' => 'abcdefghijklmnopqrstuvwxyz',
				'A-Z' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'0-9' => '0123456789',
				'1-9' => '123456789',
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
	 * Creates a Password using {@link randomString} or you
	 * pass 'human' or 'humanreadable' as $salt and then {@link $genereateHumanReadablePassword} is used
	 * 
	 * @param integer $length length of password
	 * @param string $salt	Salt String, linke {@link randomString} or 'human'
	 * @return string
	 */
	public static function generatePassword($length, $salt = null)
	{
		if (in_array($salt, array('human', 'humanreadable', 'readable'))) {
			return self::generateHumanReadablePassword($length);
		} else {
			return self::random($length, $salt);
		}
	}
	
	/**
	 * Generates a password that is readable by humans due to vocal
	 * consonant patterns. Please consider that these passwords are not
	 * very save and can be cracked easily by brute fore attacks
	 * 
	 * This method is from the 'PHP Sicherheit' Book, published by dpunkt
	 * 
	 * @param integer $length
	 * @return string
	 */
	public static function generateHumanReadablePassword($length = 12)
	{
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
	 * Converts all bytes in a string to their hex value and returns
	 * the string. You can add a spacer to add some kind of column 
	 * structure to the returned string
	 * @param string	$string	String to be as hex
	 * @param string	$spaceChar spacer string
	 * @param integer	$breakAfterBytes number of bytes when a breaks comes
	 * @return string
	 */
	public static function hex($string, $spaceChar = '', $breakAfterBytes = 0)
	{
		$strlen = self::length($string);
		$return = '';
		for ($i = 0; $i < $strlen; $i++) {
			// create hex equivalent of string value and padd it with 0 at beginning
			$return .= str_pad(sprintf('%X', ord($string[$i])), 2, '0', STR_PAD_LEFT);
			if ($breakAfterBytes == 0 ||
				$breakAfterBytes > 0 && (($i-1) % $breakAfterBytes) != 1) {
				$return .= $spaceChar;
			}
			if ($breakAfterBytes > 0 && (($i - 1) % $breakAfterBytes == 1) && $i < $strlen - 1) {
				$return .= PHP_EOL;
			}
		}
		return rtrim($return, $spaceChar);
	}
	
	/**
	 * Encodes every character in the string to HTML Encoded characters,
	 * this is multibyte save
	 * @param string $string String to encode
	 * @see htmlOrdDecode
	 * @return string
	 */
	public static function htmlOrdEncode($string)
	{
		$encodedString = '';
		if (Charset::isUTF8($string)) {
			$len = mb_strlen($string, Charset::UTF_8);
			for ($i = 0; $i < $len; $i++) {
				$char = mb_substr($string, $i, 1, Charset::UTF_8);
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
	public static function ord($c)
	{		
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
	 * Removes the repition of the same characters in a string just like
	 * the ruby method would do.
	 * @param string
	 * @return string
	 */
	public static function squeeze($string)
	{
		return preg_replace('/(\p{L}|\s){1}(\1{1,})/i', '$1', $string);	
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
	public static function each($string)
	{
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
	public static function insert($string, $position, $replaceWith = '')
	{
		return self::substr($string, 0, $position).$replaceWith.self::substr($string, $position);
	}	
}