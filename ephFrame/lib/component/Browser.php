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

/**
 * Browser Negotiation Component class
 * 
 * Usage in controller:
 * <code>
 * $this->set('Browser', $this->Browser->render());
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
class Browser extends AppComponent
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
		array('Android', array('android'), BrowserTypes::MOBILE_DEVICE),
		array('Safari (iPhone)', array('iphone'), BrowserTypes::MOBILE_DEVICE),
		array('Internet Explorer Mobile', array('windows cE', 'smartphone'), BrowserTypes::MOBILE_DEVICE),
		array('BlackBerry', array('blackberry'), BrowserTypes::MOBILE_DEVICE),
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
	 * Tests for a specific browser type
	 * 
	 * @param integer $type
	 * @return boolean
	 */
	public function isType($type)
	{
		return $this->type == $type;
	}
	
	/**
	 * Auto-detect browser on controller startup
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
	
	private function fromUserAgent($userAgentString)
	{
		// detect browser
		foreach($this->data as $data) {
			$browserName = $data[0];
			// collect regexp for browser matching
			if (!isset($data[1]) || isset($data[1]) && $data[1] === true) {
				$regexps = strtolower($browserName);
			} else {
				$regexps = $data[1];
			}
			// iterate over browser regexps
			foreach((array) $regexps as $regexp) {
				if (!preg_match('@'.preg_quote($regexp, '@').'@i', $userAgentString)) continue;
				// match
				$this->name = $browserName;
				// set browser type from match
				if (isset($data[2])) {
					$this->type = $data[2];
				}	
				break 2;
			}
		}
		
		// get browser version
		if (stristr($userAgentString, 'msie')) {
			$version = preg_match_first($userAgentString, '@MSIE[\/| ]([\\d]+\.?([\\d]+))@i');
		} else {
			$version = preg_match_first($userAgentString, '@'.$this->name.'[\/| ]([\\d]+\.?([\\d]+))@i');
		}
		// Safari Version detection
		if ($this->name == 'Safari' || $this->name == 'Android') {
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
			} elseif ($version == 412.5) {
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
		$this->version = $version;
		return $this->render();
	}	
}

/**
 * Different Browser Types
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 2009-09-29
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 */
class BrowserTypes 
{
	const BROWSER = 1;
	const MOBILE_DEVICE = 2;
	const MOBILE = 2;
	const TEXT_BASED = 4;
	const VIDEO_GAME_CONSOLE = 8;
	const OTHER = 255;
}

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