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
 * Iñtërnâtiônàlizætiøn (I18n) class
 * 
 * This class should handle all stuff that has to do with: dates, currencies,
 * translations.
 * 
 * As soon you include this class into your app you'll have new functions to
 * call translated strings: {@link __}, {@link __html}
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * @since 03.05.2008
 */
class I18n extends AppComponent 
{
	/**
	 * Current locale used
	 * @var string
	 */
	public static $locale = 'de_DE';
	
	/**
	 * Language part of the {@link locale} string, contains lowercase language
	 * code
	 * @var string
	 */
	public static $language = 'de';
	
	/**
	 * country part of the {@link locale} string, contains lowercase country
	 * code
	 * @var string
	 */
	public static $country = 'de';
	
	/**
	 * Location of locale files
	 * @var string
	 */
	public $domainLocation = 'locale/';
	
	/**
	 * Default encoding
	 * @var string
	 */
	protected $domainEncoding = 'UTF-8';
	
	/**
	 * Try to detect client language on sended accept_language request header
	 * @var boolean
	 */
	public $autoDetect = true;
	
	/**
	 * Default Domain Name that is used
	 * @var string
	 */
	public $domainName = 'default';
	
	public function startup() 
	{
		// get language from requested client header
		if ($this->autoDetect && $acceptLanguage = $this->controller->request->header->get('accept_language')) {
			$this->locale($acceptLanguage);
		// default language defined in the /app/config/config.php
		} elseif ($defaultLanguage = Registry::read('I18n.language')) {
			$this->locale($defaultLanguage);
		}
		$this->domainLocation = APP_ROOT.$this->domainLocation;
		$this->controller->data->set(get_class($this), $this);
		self::locale(self::$locale);
		$this->domain($this->domainLocation, $this->domainName, $this->domainEncoding);
		return $this;
	}
	
	public static function normalizeLocale($locale)
	{
		$locale = strtolower(substr($locale, 0, 2)).'_'.strtoupper(substr($locale, 3, 2));
		if (strlen($locale) == 3) {
			$locale .= strtoupper($locale);
		}
		return $locale;
	}
	
	/**
	 * Change the locale string or return it
	 * @param string $locale
	 * @return I18n|string
	 */
	public static function locale($locale = null, $types = array(LC_MESSAGES, LC_COLLATE, LC_TIME))
	{
		if (func_num_args() == 0) return self::$locale;
		self::$locale = self::normalizeLocale($locale);
		// set country and language 
		self::$language = substr(self::$locale,0,2);
		self::$country = strtolower(substr(self::$locale,3,2));
		foreach((array) $types as $type) {
			putenv('LC_ALL='.self::$locale);
			setlocale($type, self::$locale);
			logg(Log::VERBOSE_SILENT, 'ephFrame: Component '.__CLASS__.' setting locale \''.$type.'\' to \''.$locale.'\'');
		}
		return true;
	}
	
	public function domain($location, $name, $encoding = null)
	{
		if (!empty($location)) {
			$this->domainLocation = rtrim($location, DS).DS;
		}
		$this->domainName = $name;
		// bind textdomain
		$result = bindtextdomain($this->domainName, $this->domainLocation);
		if (empty($result)) {
			throw new I18nDomainLocationNotFoundException($this);
		}
		textdomain($this->domainName);
		// set domain name encoding if given
		if (!empty($encoding)) {
			$this->encoding = $encoding;
			$encoding = strtoupper($this->domainEncoding);
			bind_textdomain_codeset($this->domainName, $this->domainEncoding);
		}
		// log message
		$logmessage = 'ephFrame: Component '.get_class($this).' setting domain '.
			'location: \''.$result.'\', '.
			'domainname: \''.$this->domainName.'\'';
		if (!empty($encoding)) {
			$logmessage .= ', encoding: \''.$this->domainEncoding.'\'';
		}
		logg(Log::VERBOSE_SILENT, $logmessage);
		return $this;
	}
	
	public static $numberFormat = '%!.0n';
	public static $moneyFormat = '%!.0n €';
	
	/**
	 * Returns a string rendered in the current language and format
	 * set {@link locale}
	 * @param integer|float
	 * @param string optinal format to use instead of default format
	 * @return string
	 */
	public static function number($number, $format = null)
	{
		return money_format(($format === null) ? self::$numberFormat : $format, $number);
	}
	
	/**
	 * Returns a formatted money value with currency as set in the
	 * {@link moneyFormat}
	 * @param integer|float
	 * @param string optinal format to use instead of default format
	 * @return string 
	 */
	public static function money($money, $format = null)
	{
		return money_format(($format === null) ? self::$numberFormat : $format, $number);
	}
}

/**
 * Alias for _ or gettext with substitution
 * 
 * @param string $string
 * @return string
 */
function __($string)
{
	$translated = _($string);
	if (empty($translated)) {
		return $translated;
	} elseif (func_num_args() > 1) {
		$args = func_get_args();
		if (is_array($args[0])) {
			$args = $args[0];
		}
		return String::substitute($translated, $args);
	}
	return $translated;
}

/**
 * @param string $domain
 * @param string $string
 */
function __d($domain, $string)
{
	$translated = dcgettext($domain, $string);
	if (empty($translated)) {
		return $translated;
	} elseif (func_num_args() > 1) {
		$args = func_get_args();
		if (is_array($args[0])) {
			$args = $args[0];
		}
		return String::substitute($translated, $args);
	}
	return $translated;
}

/**
 * Alias for ngettext with replacement capabilities
 * 
 * @param string $singular
 * @param string $plural
 * @param integer $count
 * @return string
 */
function __n($singular, $plural, $count)
{
	$translated = ngettext($singular, $plural, $count);
	if (empty($translated)) {
		return $translated;
	}
	$args = array_slice(func_get_args(), 2);
	if (is_array($args[0])) {
		$args = $args[0];
	} else {
		array_unshift($args, $translated); // add translation as first index of array
	}
	return String::substitute($translated, $args);
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class I18nException extends ComponentException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class I18nDomainLocationNotFoundException extends I18nException 
{
	public function __construct(I18n $I18n) 
	{
		$message = 'Unable to find textdomain \''.$I18n->domainName.'\' in \''.$I18n->domainLocation.'\'';
		parent::__construct($message);
	}
}