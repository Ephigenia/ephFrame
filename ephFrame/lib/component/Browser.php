<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

loadHelper('Sanitize');

/**
 * Browser detection class
 * 
 * originally taken frome a side project<br />
 * <br />
 * check wikipedia entry for more information:
 * {@link http://en.wikipedia.org/wiki/User_agent}
 * 
 * Usage in controller:
 * <code>
 * 	$this->set('Browser', $this->Browser->render());
 * </code>
 * 
 * @TODO add newsreaders
 * @TODO add spiders and bots
 * @TODO add mobile devices like smartphones, mobiles
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 22.02.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 */
class Browser extends AppComponent implements Renderable 
{
	public $name;
	
	public $version;
	
	public $type = BrowserTypes::BROWSER;

	/**
	 * list of browser names detected in this component
	 * @var array(string)
	 */
	private $data = array(
		// portable & mobile devices
		array('Safari (iPhone)', array('iphone'), BrowserTypes::PHONE)
		array('Internet Explorer Mobile', array('windows cE', 'smartphone'), BrowserTypes::MOBILE_DEVICE),
		array('BlackBerry', array('blackberry'), BrowserTypes::MOBILE_DEVICE),
		array('Android', array('android'), BrowserTypes::MOBILE_DEVICE),
		// gaming consoles and portables
		array('Nintendo Wii', array('nintendo wii'), BrowserTypes::VIDEO_GAME_CONSOLE),
		array('Playstation Portable', true, BrowserTypes::VIDEO_GAME_CONSOLE),
		// desktop browsers
		array('Safari'),
		array('Konqueror'),
		array('Opera Mini'),
		array('Opera'),
		array('WebTV'),
		array('NetPositive'),
		array('Bonecho'),
		array('K-Melon'),
		array('Links'),
		array('Flock'),
		array('ICab'),
		array('IceWeasel'),
		array('AOL Explorer', array('America Online', 'aol')),
		array('Avant Browser'),
		array('Camino'),
		array('Amaya'),
		array('Omniweb'),
		array('Netscape'),
		array('Playstation 3'),
		array('Seamonkey'),
		array('Netscape Navigator', array('mozilla compatible')),
		array('Firefox', array('Firefox', 'Phoenix')),
		array('Internet Explorer', 'msie'),
		// terminal browser
		array('Lynx', array('lynx'), BrowserTypes::TEXT_BASED),
		array('EdBrowse', array('edbrows'), BrowserTypes::TEXT_BASED),
		array('ELinks', array('elinks'), BrowserTypes::TEXT_BASED),
		array('Emacs', array('emacs'), BrowserTypes::TEXT_BASED),
	);
	
	
	/**
	 * Enter description here...
	 * @return Browser
	 */
	public function startup() 
	{
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->fromUserAgent($_SERVER['HTTP_USER_AGENT']);
		}
		return $this;
	}
	
	public function render() 
	{
		if (!$this->beforeRender()) return false;
		return $this->afterRender(trim($this->name.' '.$this->version));
	}
	
	private function fromUserAgent($userAgentString) {
		Sanitize::panic($userAgentString);
		// detect browser
		foreach($this->data as $data) {
			$browserName = $data[0];
			// collect regexp for browser matching
			if (!isset($data[1]) || isset($data[1]) && $data[1] == true) {
				$regexps = strtolower($browserName);
			} elseif (!is_array($data[1])) {
				$regexps = array($data[1]);
			} else {
				$regexps = $data[1];
			}
			// go through regexps and find a matching browser
			foreach($regexps as $regexp) {
				if (!preg_match('@'.$regexp.'@i', $regexp)) continue;
				// match
				$this->name = $browserName;
				if (isset($data[2])) {
					$this->type = $data[2];
				}
			}
		}
		// get browser version with regexp if browser != safari 
		if ($this->name !== 'Safari') {
			if (stristr($userAgentString, 'msie')) {
				$this->version = preg_match_first('@MSIE[\/| ]([\\d]+\.?([\\d]+))@i');
			} else {
				$this->version = preg_match_first('@'.$this->name.'[\/| ]([\\d]+\.?([\\d]+))@i');
			}
		// Safari Version detection
		} else {
			$version = (float) $version;
			if ($version >= 523.1) {
				$version = '3.0.4';
			} else if ($version > 419.3) {
				$version = '3.0';
			} elseif ($version > 418) {
				$version = '2.0.4';
			} elseif ($version > 417.7) {
				$version = '2.0.3';
			} elseif ($version > 412.5) {
				$version = '2.0.2';
			} elseif ($verison == 412.5) {
				$version = '2.0.1';
			} elseif ($version > 312.6) {
				$version = '2.0';
			} elseif ($version > 312.5) {
				$version = '1.3.2';
			} elseif ($version > 312) {
				$version = '1.3.1';
			} elseif ($version > 125.12) {
				$version = '1.3';
			} elseif ($version > 125.9) {
				$version = '1.2.4';
			} elseif ($version > 125.8) {
				$version = '1.2.3';
			} elseif ($version > 100.1) {
				$version = '1.2.2';
			} elseif ($version > 100) {
				$version = '1.1.1';
			} elseif ($version > 85.9) {
				$version = '1.1';
			} elseif ($version > 85.7) {
				$version = '1.0.3';
			} elseif ($version > 85.5) {
				$version = '1.0.2';
			} elseif ($version > 0) {
				$version = '1.0';
			}
		}
	}	
}

/**
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 2009-09-29
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 */
class BrowserTypes 
{
	
	const DEFAULT = BrowserTypes::BROWSER;
	
	const BROWSER = 1;
	const MOBILE_DEVICE = 2;
	const PHONE = 4;
	const TEXT_BASED = 8;
	const VIDEO_GAME_CONSOLE = 16;
	const OTHER = 255;
	
} // END BrowserTypes class

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class BrowserException extends BasicException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class BrowserNotFoundException extends BrowserException 
{
	public function __construct($id) 
	{
		parent::__construct('No Browser found with the given id');	
	}
}