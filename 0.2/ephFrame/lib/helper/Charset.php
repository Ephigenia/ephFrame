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
 * Charset
 * 
 * This is partially tested in {@link TestCharset}.
 * 
 * Take care that this file is always saves in utf8 format!
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 02.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 * @version 0.2
 */
class Charset extends Helper {
	
	/**
	 * Checks if a string is ASCII
	 * @param string $string
	 * @return boolean
	 */
	public static function isASCII($string) {
		return (bool) (preg_match('/^[\\x00-\\x7A]*$/', $string));
	}
	
	public static $iso88591replaceArr = array();
	public static $iso88591ToUtf8 = array();
	public static $utf8replaceArr = array();
	
	
	/**
	 * Convert passed $string into ASCII Charset using iconv if
	 * available.	
	 *
	 * @param string $string
	 * @return string
	 */
	public static function toASCII($string) {
		$string = self::toSingleBytes($string);
		if (function_exists('iconv')) {
			if (self::isUTF8($string) && ($result = @iconv('UTF-8', 'ASCII//TRANSLIT', $string))) {
				return $result;
			} elseif ($result = @iconv('ISO-8859-1', 'ASCII', $string)) {
				return $result;
			}
		}
		return $string;
	}
	
	/**
	 * Fixes wrong utf8 entities to their original entitiy
	 * @return string
	 */
	public static function toUtf8($string) {
		self::$iso88591ToUtf8 = array(
			chr(228) => 'ä', chr(196) => 'Ä',
			chr(0xF6) => 'ö', chr(0xD6) => 'Ö',
			chr(0xFC) => 'ü', chr(0xDC) => 'Ü', 
			chr(0xDF) => 'ß'
			// è			  é					ê
//			chr(0xE8) => 'è', chr(0xE9) => 'é', chr(0xEA) => 'ê',
//			// È			  É					Ê
//			chr(0xC8) => 'È', chr(0xC9) => 'É', chr(0xCA) => 'Ê',
//			// à			  á					â
//			chr(0xE0) => 'à', chr(0xE1) => 'á', chr(0xE2) => 'â',
//			// À			  Á					Â
//			chr(0xC0) => 'À', chr(0xC1) => 'Á', chr(0xC2) => 'Â',
//			// ò			  ó					ô
//			chr(0xF2) => 'ò', chr(0xF3) => 'ó', chr(0xF4) => 'ô',
//			// Ò			  Ó					Ô
//			chr(0xD2) => 'Ò', chr(0xD3) => 'Ó', chr(0xD4) => 'Ô',
//			// ì			  í				    î
//			chr(0xEC) => 'ì', chr(0xED) => 'í', chr(0xEE) => 'î',
//			// Ì			  Í					Î
//			chr(0xCC) => 'Ì', chr(0xCD) => 'Í', chr(0xCE) => 'Î',
//			// ù			  ú					û
//			chr(0xF9) => 'ù', chr(0xFA) => 'ú', chr(0xFB) => 'û',
//			// Ù			  Ú					Û
//			chr(0xD9) => 'Ù', chr(0xDA) => 'Ú', chr(0xDB) => 'Û',
//			// ©			  ®
//			chr(0xA9) => '©', chr(0xAE) => '®',
//			// « 			  »
//			chr(0xAB) => '«', chr(0xBB) => '»'
		);
		return strtr($string, self::$iso88591ToUtf8);
	}
	
	/**
	 * Translate multibyte characters in a $string to single byte characters
	 * using a conversion array.
	 * 
	 * <code>
	 * // should echo 'Egalite is faebulous';
	 * echo Charset::toSingleBytes('Égalité is fäbulous');
	 * </code>
	 * 
	 * @param string $string
	 * @return string
	 */
	public static function toSingleBytes($string) {
		// create charset table cache
		if (empty(self::$utf8replaceArr)) {
			self::$utf8replaceArr = array(
				'ä' => 'ae', 'Ä' => 'AE',
				'ö' => 'oe', 'Ö' => 'OE',
				'ü' => 'ue', 'Ü' => 'UE',
				'ß' => 'ss',
				'è' => 'e', 'é' => 'e', 'ê' => 'e',
				'È' => 'E', 'É' => 'E', 'Ê' => 'E',
				'à' => 'a', 'á' => 'a', 'â' => 'a',
				'À' => 'A', 'Á' => 'A', 'Â' => 'a',
				'ò' => 'o', 'ó' => 'o', 'ô' => 'o',
				'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
				'ì' => 'i', 'í' => 'i', 'î' => 'i',
				'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
				'ú' => 'u', 'ù' => 'u', 'û' => 'u',
				'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U',
				'˝' => '"',
				'‶' => '"',
				'″' => '"',
				'“' => '"',
				'”' => '"',
				'„' => '"',
				'‟' => '"',
				'‘' => '\'',
				'’' => '\'',
				'‛' => '\''
			);
			self::$iso88591replaceArr = array(
				chr(228) => 'ae', chr(196) => 'AE',
				chr(0xF6) => 'oe', chr(0xD6) => 'OE',
				chr(0xFC) => 'ue', chr(0xDC) => 'UE', 
				chr(0xDF) => 'ss',
				// è			  é					ê
				chr(0xE8) => 'e', chr(0xE9) => 'e', chr(0xEA) => 'e',
				// È			  É					Ê
				chr(0xC8) => 'E', chr(0xC9) => 'E', chr(0xCA) => 'E',
				// à			  á					â
				chr(0xE0) => 'a', chr(0xE1) => 'a', chr(0xE2) => 'a',
				// À			  Á					Â
				chr(0xC0) => 'A', chr(0xC1) => 'A', chr(0xC2) => 'A',
				// ò			  ó					ô
				chr(0xF2) => 'o', chr(0xF3) => 'o', chr(0xF4) => 'o',
				// Ò			  Ó					Ô
				chr(0xD2) => 'O', chr(0xD3) => 'O', chr(0xD4) => 'O',
				// ì			  í				    î
				chr(0xEC) => 'i', chr(0xED) => 'i', chr(0xEE) => 'i',
				// Ì			  Í					Î
				chr(0xCC) => 'I', chr(0xCD) => 'I', chr(0xCE) => 'I',
				// ù			  ú					û
				chr(0xF9) => 'u', chr(0xFA) => 'u', chr(0xFB) => 'u',
				// Ù			  Ú					Û
				chr(0xD9) => 'U', chr(0xDA) => 'U', chr(0xDB) => 'U',
				// 
			);
		}
		assert(!is_object($string) && !is_resource($string));
		$string = strtr($string, self::$utf8replaceArr);
		$string = strtr($string, self::$iso88591replaceArr);
		return $string;
	}
	
	/**
	 * Tests if a string is utf8 encoded.
	 * 
	 * This will use the iconv support if installed/compiled with php, which
	 * is much faster than the regular expression match that is performed if
	 * iconv is not installed.<br />
	 * This function is from O'Reilly - Building Scalable Websites
	 * @param	string	$string
	 * @return boolean
	 */
	public static function isUTF8($string) {
		if (function_exists('iconv')) {
			return (iconv('UTF-8', 'UTF-8', $string) == $string);
		} else {
			$regexp = '[\xC0-\xDF](^\x80-\xBF]|$)'.
				'|[\xE0-\xEF].{0,1}([^\x80-\xBF]|$)'.
				'|[\xF0-\xF7].{0,2}([^\x80-\xBF]|$)'.
				'|[\xF8-\xFB].{0,3}([^\x80-\xBF]|$)'.
				'|[\xFC-\xFD].{0,4}([^\x80-\xBF]|$)'.
				'|[\xFE-\xFE].{0,5}([^\x80-\xBF]|$)'.
				'|[\x00-\x7F][\x80-\xBF]'.
				'|[\xC0-\xDF].[\x80-\xBF]'.
				'|[\xE0-\xEF]..[\x80-\xBF]'.
				'|[\xF0-\xF7]...[\x80-\xBF]'.
				'|[\xF8-\xFB]....[\x80-\xBF]'.
				'|[\xFC-\xFD].....[\x80-\xBF]'.
				'|[\xFE-\xFE]......[\x80-\xBF]'.
				'|^[\x80-\xBF]';
			return preg_match('!'.$regexp.'!', $string);
		}
	}
	
	public static function encodingName($encoding) {
		switch($encoding) {
			default:
			case self::UTF_8:
				return 'utf-8';
			case self::ISO_8859_1:
				return 'ISO-8869-1';
		}
	}
	
	const UTF_8 = 'utf-8';
	const UTF_16_Big_Endian = 2;
	const UTF_16_Little_Endian = 3;
	const UTF_32_Big_Endian = 4;
	const UTF_32_Little_Endian = 5;
	const SCSU = 6;
	const UTF_EBCDIC = 7;
	const BOCU_1 = 8;
	const ISO_8859_1 = 'ISO-8859-1';
	
	/**
	 * Returns encoding detected by <a href="http://de.wikipedia.org/wiki/Byte_Order_Mark">BOM</a>
	 * or false if no bom was found
	 * @return string|boolean
	 */
	public static function BOM($string) {
		$bom = false;
		$strlen = strlen($string);
		$hex = String::hex(substr($string,0,8));
		if (substr($hex, 0, 4) == 'EFBBBF') {
			return self::UTF_8;
		} elseif (substr($hex, 0, 4) == 'FEFF') {
			return self::UTF_16_Big_Endian;
		} elseif (substr($hex, 0, 4) == 'FFFE') {
			return self::UTF_16_Little_Endian;
		} elseif ($hex == '0000FEFF') {
			return self::UTF_32_Big_Endian;
		} elseif ($hex == 'FFFE0000') {
			return self::UTF_32_Little_Endian;
		}  elseif (substr($hex, 0, 6) == '0EFEFF') {
			return self::SCSU;
		} elseif ($hex == 'DD736673') {
			return self::UTF_EBCDIC;
		}  elseif (substr($hex, 0, 6) == 'FBEE28') {
			return self::BOCU_1;
		}
		return $bom;
	}
	
}