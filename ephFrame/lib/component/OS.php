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
 * OS Detecting Class
 * 
 * can parse Operating system out of an user agent string transfered by the
 * browser.
 * 
 * Usage:
 * <code>
 * 	echo OS($_SERVER["HTTP_USER_AGENT"]);
 * </code>
 * 
 * // TODO Find out windows vista os codes
 * // @todo add isMobile to this class ('(iPhone|MIDP|AvantGo|BlackBerry|J2ME|Opera Mini|DoCoMo|NetFront|Nokia|PalmOS|PalmSource|portalmmm|Plucker|ReqwirelessWeb|SonyEricsson|Symbian|UP\.Browser|Windows CE|Xiino)')
 * 
 * @author	Marcel Eichner // Ephigenia <love at ephigenia dot de>
 * @since 22.02.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 */
class OS extends AppComponent implements Renderable 
{	
	public $name = 'unknown';
	public $id	= 0;
	
	/**
	 * OSes that can be detected by this class
	 * @var array(string)
	 */
	private $OSs = array(
		1 => 'Sun OS',
		2 => 'BSD',
		3 => 'Linux',
		4 => 'UNIX',
		5 => 'Nintendo Wii',
		6 => 'Playstation 3',
		7 => 'Playstation Portable',
		8 => 'OS X (intel)',
		9 => 'OS X (ppc)',
		22 => 'OS X',
		27 => 'OS X (ipod)',
		28 => 'OS X (iphone)',
		10 => 'Windows Vista',
		11 => 'Windows Server 2003, Windows XP x64 Edition',
		12 => 'Windows 3.11',
		13 => 'Windows XP',
		14 => 'Windows 2000, Service Pack 1 (SP1)',
		15 => 'Windows 2000',
		16 => 'Windows NT 4.0',
		17 => 'Windows Me',
		18 => 'Windows 98',
		19 => 'Windows 05',
		20 => 'Windows CE',
		21 => 'Windows',
		23 => 'Umbuntu',
		24 => 'BeOs',
		25 => 'Palm OS',
		26 => 'OS/2'
	);

	public function startup($stringOrId = null) 
	{
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->fromUserAgent($_SERVER['HTTP_USER_AGENT']);
		}
		return $this;
	}
	
	public function beforeRender() 
	{
		return true;
	}
	
	public function afterRender($rendered) 
	{
		return $rendered;
	}
	
	public function render() 
	{
		if (!$this->beforeRender()) {
			return false;
		}
		return $this->afterRender($this->__toString());
	}
	
	public function __toString() 
	{
		return $this->name;
	}
	
	private function id($id) {
		$this->name = $this->OSs[$id];
		$this->id = $id;
	}
	
	/**
	 * Get Information about the Operating System out of the User Agent String
	 * this is normally send by the browser to the server as information
	 *
	 * @param string $userAgentString
	 */
	public function fromUserAgent($userAgentString) 
	{
		Sanitize::panic($userAgentString);
		if (stristr($userAgentString, 'sunos')) {
			$this->id(1);
		} elseif (stristr($userAgentString, 'os/2')) {
			$this->id(26);
		} elseif (stristr($userAgentString, 'freebsd') || stristr($userAgentString, 'openbsd')) {
			$this->id(2);
		} elseif (stristr($userAgentString, 'beos')) {
			$this->id(3);
		} elseif (stristr($userAgentString, 'palm')) {
			$this->id(25);
		} elseif (stristr($userAgentString, 'ubuntu')) {
			$this->id(23);
		} elseif (stristr($userAgentString, 'linux')) {
			$this->id(3);
		} elseif (stristr($userAgentString, 'x11')) {
			$this->id(4);
		} elseif (stristr($userAgentString, 'nintendo wii')) {
			$this->id(5);
		} elseif (stristr($userAgentString, 'playstation 3')) {
			$this->id(6);
		} elseif (stristr($userAgentString, 'playstation portable')) {
			$this->id(7);
		} elseif (stristr($userAgentString, 'mac') || stristr($userAgentString, 'ppc')) {
			$this->id(22);
			if (stristr($userAgentString, 'intel')) {
				$this->id(8);
			} elseif (stristr($userAgentString, 'ppc') || stristr($userAgentString, 'powerpc')) {
				$this->id(9);
			} elseif (stristr($userAgentString, 'ipod')) {
				$this->id(27);
			} elseif (stristr($userAgentString, 'iphone')) {
				$this->id(28);
			}
		} elseif (stristr($userAgentString, 'win')) {
			$this->id(21);
			if (stristr($userAgentString, 'windows nt 6.0') || stristr($userAgentString, 'vista')) {;
				$this->id(10);
			} elseif (stristr($userAgentString, 'windows nt 5.02')) {
				$this->id(11);
			} elseif (stristr($userAgentString, 'win3.11') || stristr($userAgentString, 'windows 3.11')) {
				$this->id(12);
			} elseif (stristr($userAgentString, 'windows nt 5.1')) {
				$this->id(13);
			} elseif (stristr($userAgentString, 'windows nt 5.01')) {
				$this->id(14);
			} elseif (stristr($userAgentString, 'windows nt 5.0')) {
				$this->id(15);
			} elseif (stristr($userAgentString, 'windows nt 4.0')) {
				$this->id(16);
			} elseif (stristr($userAgentString, 'windows 98; Win 9x 4.9')) {
				$this->id(17);
			} elseif (stristr($userAgentString, 'windows 98') || stristr($userAgentString, 'win98')) {
				$this->id(18);
			} elseif (stristr($userAgentString, 'windows 95') || stristr($userAgentString, 'win95')) {
				$this->id(19);
			} elseif (stristr($userAgentString, 'windows ce')) {
				$this->id(20);
			}
		}
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class OSException extends ComponentException
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class OSNotFoundException extends OSException 
{
	public function __construct($id) 
	{
		parent::__construct('No OS found with the id \''.$id.'\'');
	}
}