<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright (c) 2007+, Ephigenia M. Eichner
 * 						 Kopernikusstr. 8
 * 						 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 
 * 	@license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright		copyright 2007+, Ephigenia M. Eichner
 * 	@link			http://code.ephigenia.de/projects/ephFrame/
 * 	@version		$Revision$
 * 	@modifiedby		$LastChangedBy$
 * 	@lastmodified	$Date$
 * 	@filesource		$HeadURL$
 */

/**
 *	Iñtërnâtiônàlizætiøn (I18n) class
 * 
 * 	This class should handle all stuff that has to do with: dates, currencies,
 * 	translations.
 * 
 * 	As soon you include this class into your app you'll have new functions to
 * 	call translated strings: {@link __}, {@link __html}
 * 
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component
 * 	@author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * 	@since 03.05.2008
 */
class I18n extends Component {
	
	/**
	 * 	Current locale used
	 * 	@var string
	 */
	public static $locale = 'de';
	
	/**
	 * 	Location of locale files
	 * 	@var string
	 */
	public $domainLocation = 'locale/';
	
	/**
	 * 	Default encoding
	 * 	@var string
	 */
	protected $domainEncoding = 'UTF-8';
	
	public $domainName = 'default';
	
	public function startup() {
		if ($acceptLanguage = $this->controller->request->header->get('accept_language')) {
			self::$locale = strtolower(substr($acceptLanguage, 0, 2));
		}
		if (!preg_match('@\w{2}@', self::$locale) && defined('DEFAULT_LANGUAGE')) {
			self::$locale = DEFAULT_LANGUAGE;
		}
		$this->domainLocation = APP_ROOT.'/locale/';
		$this->controller->data->set(get_class($this), $this);
		self::locale(self::$locale);
		$this->domain($this->domainLocation, $this->domainName, $this->domainEncoding);
		return $this;
	}
	
	/**
	 * 	Change the locale string
	 * 	@param string $locale
	 * 	@return I18n
	 */
	public static function locale($locale = null) {
		if ($locale === null || func_num_args() == 0) {
			return self::$locale;
		}
		assert(is_string($locale) && !empty($locale));
		$localeType = LC_MESSAGES;
		self::$locale = $locale;
		setlocale($localeType, self::$locale);
		logg(Log::VERBOSE_SILENT, 'ephFrame: Component '.__CLASS__.' setting locale \''.$localeType.'\' to \''.$locale.'\'');
		return true;	
	}
	
	public function domain($location, $name, $encoding = null) {
		assert(is_string($location) && is_string($name));
		if (!empty($location) && substr($location, -1, 1) !== DS) {
			$location .= DS;
		}
		$this->domainLocation = $location;
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

/**
 *	Alias for _ or gettext with more fancy features
 * 	@param string $string
 * 	@param mixed $additional
 * 	@return string
 */
function __($string) {
	$translated = _($string);
	if (empty($translated)) {
		return $translated;
	}
	if (func_num_args() > 1) {
		$args = func_get_args(1);
		return String::substitute($translated, $args);
	}
	return $translated;
}

/**
 * 	Works just like {@link __} but creating html save content, with line brakes
 * 	and & encoded
 * 	@param string $string
 * 	@return string
 */
function __html($string) {
	$args = func_get_args();
	$translated = call_user_func_array('__', $args);
	$translated = preg_replace('/[\r\n]/', '<br />', $translated);
	// @todo find a better regular expression to replace & with amp;
	$translated = preg_replace('@&(?!(amp;|#\d{2,}))@i', ' &amp; ', $translated);
	return $translated;
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class I18nException extends ComponentException {}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class I18nDomainLocationNotFoundException extends I18nException {
	public function __construct(I18n $I18n) {
		$message = 'Unable to find textdomain \''.$I18n->domainName.'\' in \''.$I18n->domainLocation.'\'';
		parent::__construct($message);
	}
}

?>