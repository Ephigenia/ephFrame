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
 * 	Iñtërnâtiônàlizætiøn (i13n) class
 * 
 * 	this class handles sc_locale stuff, money, integer formats and can
 * 	be used to print and save dates, money values, integers, time in the format
 * 	that is appropriate for the country.
 * 	
 * 	@todo the name of this class seemes to be wrong?
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 15.09.2007
 * 	@version 0.1
 * 	@subpackage ephFrame.lib.helper
 *	@package ephFrame
 */
class I13n extends Helper {
	
	public static $locale;
	
	public static $numberFormat = '%!.0n';
	public static $moneyFormat = '%!.0n €';
	
	/**
	 *	Set global locale value
	 * 	@param string
	 */
	public static function locale($localeString) {
		self::$locale = $localeString;
		setlocale(LC_ALL, $localeString);
	}
	
	/**
	 * 	Returns a string rendered in the current language and format
	 * 	set {@link locale}
	 * 	@param integer|float
	 * 	@param string optinal format to use instead of default format
	 * 	@return string
	 */
	public static function number($number, $format = null) {
		return money_format(($format === null) ? self::$numberFormat : $format, $number);
	}
	
	/**
	 * 	Returns a formatted money value with currency as set in the
	 * 	{@link moneyFormat}
	 * 	@param integer|float
	 * 	@param string optinal format to use instead of default format
	 * 	@return string 
	 */
	public static function money($money, $format = null) {
		return money_format(($format === null) ? self::$numberFormat : $format, $number);
	}
	
}

?>