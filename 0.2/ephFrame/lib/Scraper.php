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

class_exists('CURL') or require dirname(__FILE__).'/CURL.php';

/**
 * 	Class that loads webpages and returns the content
 * 	
 *	@author Ephigenia // Marcel Eichner <love@ephigenia.de>
 *	@since 30.04.2009
 *	@package ephFrame
 *	@subpackage ephFrame.lib
 */
class Scraper extends CURL {
	
	public function read($url = null) {
		$this->url = is_null($url) ? $this->url : $url;
		$this->followLocation = true;
		return $this->exec(true, false);
	}
	
	public static function scrape($url = null) {
		$s = new Scraper($url);
		return $s->read();
	}

}

?>