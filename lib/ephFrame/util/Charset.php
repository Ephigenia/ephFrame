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
	const UTF_8 = 'UTF-8';

	/**
	 * Checks if a string is ASCII
	 * @param string $string
	 * @return boolean
	 */
	public static function isASCII($string)
	{
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
		return (bool) mb_check_encoding($string, self::UTF_8);
	}
}