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

// Load String Class that is needed
ephFrame::loadClass('ephFrame.lib.helper.String');

/**
 * Sanitizer
 * 
 * The Sanitizer takes strings and arrays of string and converts them for a
 * display in a specifice content.
 * 
 * So for example you want to save a blog comment with username, email and
 * text. The Text should allow some html tags but encode every not allowed tag
 * including for example <script>-Tags.<br />
 * We would then include this in the Save-Method in the CommentController:
 * <code>
 * public function save() {
 * 	// sanitize submitted data
 * 	Sanitize::sanitize($this->request->data, Sanitize::HTML, array('text', 'email'));
 * }
 * </code>
 * 
 * The Sanitizer also can clean strings up for you with the {@link clean}
 * method which also accepts flags as parameter.
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 10.06.2007
 * @uses String
 */
class Sanitizer extends Helper {
	
	const PANIC = 1;
	const PARANOID = 2;
	const SQL = 4;
	const SYSTEM = 8;
	const HTML = 16;
	const INT = 32;
	const FLOAT = 64;
	const FILENAME = 128;
	
	const CLEAN_CARRIAGE = 1;
	const CLEAN_SPACES = 2;
	const CLEAN_UNICODE = 4;
	const CLEAN_DOLLAR = 8;
	const CLEAN_HTML = 16;
	const CLEAN_CONTROL_CHARS = 32;
	const CLEAN_ALL = 255;
	
	public static function clean(&$var, $flags = null, $ignore = null) {
		if ($flags == null) {
			$flags = self::CLEAN_ALL;
		}
		if (is_array($var)) {
			foreach($var as $i => $v) {
				if ($ignore !== null && in_array($i, $ignore)) continue;
				$var[$i] = self::clean($v, $flags, $ignore);
			}
			return $var;
		}
		$var = trim($var);
		// always drop 00-strings
		$var = preg_replace('@\x00@', '', $var);
		if ($flags & self::CLEAN_CARRIAGE) {
			$var = preg_replace('@\r+@', '', $var);
		}
		if ($flags & self::CLEAN_SPACES) {
			$var = preg_replace('@[\s+]@', ' ', $var);
		}
		if ($flags & self::CLEAN_UNICODE) {
			$var = preg_replace("@&#([0-9]+);@s", "&#\\1;", $var);
		}
		if ($flags & self::CLEAN_DOLLAR) {
			$var = str_replace('\\\$', '$', $var);	
		}
		if ($flags & self::CLEAN_HTML) {
			$var = String::stripTags($var);
		}
		if ($flags & self::CLEAN_CONTROL_CHARS) {
			$var = String::stripControlChars($var);
		}
		return $var;
	}

	public static function sanitize($var, $flags = null, $include = null) {
		if ($flags == null) {
			$flags = self::PARANOID; 
		}
		$var = self::clean($var);
		if (is_array($var)) {
			foreach($var as $i => $v) {
				if ($include == null || ($include !== null && in_array($i, $include))) {
					$var[$i] = self::sanitize($v, $flags, $include);
				}
			}
			return $var;
		}
		if ($flags & self::PANIC) $var = self::panic($var);
		if ($flags & self::PARANOID) $var = self::paranoid($var);
		if ($flags & self::SQL) $var = self::sql($var);
		if ($flags & self::SYSTEM) $var = self::system($var);
		if ($flags & self::HTML) $var = self::html($var);
		if ($flags & self::INT) $var = self::int($var);
		if ($flags & self::FLOAT) $var = self::float($var);
		if ($flags & self::FILENAME) $var = self::filename($var);
		return $var;
	}
	
	public static function panic(&$var, $ignore = null) {
		if (is_array($var)) return self::sanitize($var, self::PANIC, $ignore);
		$var = preg_replace('@[^0-9a-z_-]@i', '', $var);
		return $var;
	}
	
	public static function paranoid($string, $additionalChars = null) {
		$allow = 'a-zA-Zà-úÀ-Ú!?*™˝©®+-_%&\/|(){}[]$€£₤¥¡¿#§@:;⁏.,‚~‘’‛“”„‟°˝`´‵‶′″"\'';
		if ($additionalChars === null) {
			$allow .= $additionalChars;
		}
		return preg_replace('@[^'.preg_quote($allow, '@').']@i', '', $string);
	}
	
	/**
	 *	Cleans $string to be a valid filename on every system
	 *	- stripping trailing and leading dots
	 *  - replace special characters (that are not asc-ii)
	 *	- replace space with underscores
	 *	- limit the length of the filename to 255 chars
	 *	- strip path information
	 * 	@param $string
	 *	@return string
	 */
	public static function filename($string) {
		$filename = String::toURL(trim(basename($string, '.')), '_');
		if (($dotPos = strpos($filename, '.')) !== false) {
			$extension = substr($filename, $dotPos + 1);
			$basename  = substr($filename, 0, $dotPos);
			return substr($basename, 0, 230 - strlen($extension) - 1).'.'.$extension;
		}
		return String::substr($filename, 0, 255);
	}
	
	/**
	 * @param $string
	 * @return string
	 */
	public static function sql($string) {
		if (is_array($string)) return self::sanitize($string, self::SQL);
		return mysql_real_escape_string($string);
	}
	
	public static function system($string) {
		if (is_array($string)) return self::sanitize($string, self::SYSTEM);
		return escapeshellcmd($string);
	}
	
	public static function html($string, $allowedTags = array()) {
		// first replace various encodings of < and > back to < and >
		$string = preg_replace('@%3C|&gt;?|&#0*60;?|&#x0*3C;?|\\\x3C|\\\u003C@', '<', $string);
		$string = preg_replace('@%3E|&lt;?|&#0*62;?|&#x0*3E;?|\\\x3E|\\\u003E@', '>', $string);
		$string = preg_replace('@&(?!(amp;|#\d{2,}))@i', '&amp;', $string);
		// then strip not allowed tags
		if (!is_array($allowedTags)) {
			$allowedTagsString = '';
		} else {
			$allowedTagsString = '';
			foreach($allowedTags as $tagname) {
				$allowedTagsString .= '<'.$tagname.'></'.$tagname.'>';
			}
		}
		$string = strip_tags($string, $allowedTagsString);
		// strip attributes
		return $string;
	}
	
	public static function int($string, $min = null, $max = null) {
		if (is_array($string)) return self::sanitize($string, self::INT);
		$int = intval($string);
		if (($min !== null && $int < $min) || ($max !== null && $int > $max)) {
			return false;
		}
		return $int;
	}
	
	public static function float($string, $min = null, $max = null) {
		if (is_array($string)) return self::sanitize($float, self::SQL);
		$int = floatval($string);
		if (($min !== null && $int < $min) || ($max !== null && $int > $max)) {
			return false;
		}
		return $int;
	}
	
}