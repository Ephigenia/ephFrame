<?php

namespace ephFrame\util;

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
class Charset
{
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
	 * Checks if a string is ASCII
	 * @param string $string
	 * @return boolean
	 */
	public static function isASCII($string) {
		return (bool) (preg_match('/^[\\x00-\\x7A]*$/', $string));
	}
	
	/**
	 * Tests if a string is utf8 encoded.
	 * 
	 * This will use the iconv support if installed/compiled with php, which
	 * is much faster than the regular expression match that is performed if
	 * iconv is not installed.<br />
	 * This function is from O'Reilly - Building Scalable Websites
	 * 
	 * @param	string	$string
	 * @return boolean
	 */
	public static function isUTF8($string)
	{
		if (function_exists('iconv')) {
			return (@iconv('UTF-8//IGNORE', 'UTF-8//IGNORE', $string) == $string);
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
}