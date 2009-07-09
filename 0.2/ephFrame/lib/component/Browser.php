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
class Browser extends Component implements Renderable {
	
	public $name = 'unknown';
	public $version;
	public $id = 0;

	/**
	 * Browsers supported during Detection
	 * @var array(string)
	 */
	private $Browsers = array(
		1	=> 'Konqueror',
		2	=> 'Safari',
		3	=> 'Opera',
		4	=> 'WebTV',
		5 	=> 'Firefox',
		6	=> 'Internet Explorer',
		17	=> 'Internet Explorer Mobile',
		7	=> 'OmniWeb',
		8	=> 'Netscape',
		9	=> 'Netscape Navigator',
		10	=> 'AOL Explorer',
		11	=> 'Amaye',
		12	=> 'Camino',
		13	=> 'Flock',
		14	=> 'iCab',
		15	=> 'Iceweasel',
		16 	=> 'Avant Browser',
		18	=> 'K-Meleons',
		19	=> 'Links',
		20	=> 'Lynx',
		21	=> 'NetPositive',
		22	=> 'Novarra',
		23	=> 'Opera Mini',
		24	=> 'Playstation 3',
		25	=> 'Playstation Portable',
		26	=> 'SeaMonkey',
		27	=> 'BonEcho'
	);
	
	/**
	 * Enter description here...
	 * @return Browser
	 */
	public function startup() {
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->fromUserAgent($_SERVER['HTTP_USER_AGENT']);
		}
		return $this;
	}
	
	public function beforeRender() {
		return true;
	}
	
	public function afterRender($rendered) {
		return $rendered;
	}
	
	public function render() {
		if (!$this->beforeRender()) return false;
		$rendered = '';
		if (!empty($this->version) && !empty($this->name)) {
			$rendered = $this->name.' '.$this->version;
		} elseif (!empty($this->name)) {
			$rendered = $this->name;
		}
		return $this->afterRender($rendered);
	}
	
	/**
	 * Returns Browser Name and version if found
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}
	
	private function id($id) {
		$this->name = $this->Browsers[$id];
		$this->id = $id;
	}
	
	/**
	 * @param string $userAgentString
	 */
	private function fromUserAgent($userAgentString) {
		Sanitize::panic($userAgentString);
		if (stristr($userAgentString, 'konqueror')) {
			$this->id(1);
		} elseif (stristr($userAgentString, 'safari')) {
			$this->id(2);
		} elseif (stristr($userAgentString, 'opera mini')) {
			$this->id(23);
		} elseif (stristr($userAgentString, 'opera')) {
			$this->id(3);
		} elseif (stristr($userAgentString, 'webtv')) {
			$this->id(4);
		} elseif (stristr($userAgentString, 'netpositive')) {
			$this->id(21);
		} elseif (stristr($userAgentString, 'bonecho')) {
			$this->id(27);
		} elseif (stristr($userAgentString, 'k-meleon')) {
			$this->id(18);
		} elseif (stristr($userAgentString, 'links')) {
			$this->id(19);
		} elseif (stristr($userAgentString, 'lynx')) {
			$this->id(20);
		} elseif (stristr($userAgentString, 'msie') && (stristr($userAgentString, 'windows ce') || stristr($userAgentString, 'smartphone'))) {
			$this->id(17);
		} elseif (stristr($userAgentString, 'flock')) {
			$this->id(13);
		} elseif (stristr($userAgentString, 'icab')) {
			$this->id(14);
		} elseif (stristr($userAgentString, 'iceweasel')) {
			$this->id(15);
		} elseif (stristr($userAgentString, 'firefox') || stristr($userAgentString, 'phoenix')) {
			$this->id(5);
		} elseif (stristr($userAgentString, 'america online')) {
			$this->id(10);
		} elseif (stristr($userAgentString, 'avant browser')) {
			$this->id(16);
		} elseif (stristr($userAgentString, 'camino')) {
			$this->id(12);
		} elseif (stristr($userAgentString, 'amaya')) {
			$this->id(11);
		} elseif (stristr($userAgentString, 'msie')) {
			$this->id(6);
		} elseif (stristr($userAgentString, 'omniweb')) {
			$this->id(7);
		} elseif (stristr($userAgentString, 'netscape')) {
			$this->id(8);
		} elseif (stristr($userAgentString, 'playstation 3')) {
			$this->id(24);
		} elseif (stristr($userAgentString, 'playstation portable')) {
			$this->id(25);
		} elseif (stristr($userAgentString, 'seamonkey')) {
			$this->id(26);
		} elseif (stristr($userAgentString, 'mozilla') && !stristr($userAgentString, 'compatible')) {
			$this->id(9);
		}
		/**
		 * Parse Version Number
		 * is either [BrowserName] [version] or [BrowserName]/[Version]
		 */ 
		if (!empty($this->id)) {
			$browser = $this->name;
			if (stristr($userAgentString, 'msie')) $browser = 'MSIE';
			$regexp = '/'.$browser.'[\/| ]([\\d]+\.?([\\d]+))/';
			if (preg_match($regexp, $userAgentString, $found)) {
				$version = $found[1];
				/**
				 * Parse Safari version numbers, depending on webkit version documented
				 * here: http://developer.apple.com/internet/safari/uamatrix.html
				 */
				if ($this->id == 2) {
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
				$this->version = $version;
			// browser not found
			} else {
				
			}
		}
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class BrowserException extends BasicException {}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class BrowserNotFoundException extends BrowserException {
	public function __construct($id) {
		parent::__construct('No Browser found with the given id');	
	}
}