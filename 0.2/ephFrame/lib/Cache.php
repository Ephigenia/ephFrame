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

ephFrame::loadInterface('ephFrame.lib.component.Cache.CacheEngine');

/**
 * 	Cache
 * 
 * 	Create a file cache engine and store some data:
 * 	<code>
 *  $fileCache = new Cache('file');
 * 	</code>
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 25.09.2007
 * 	@version 0.1
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class Cache extends Object {
	
	public $engine;
	
	public $defaultExpire = HOUR;
	
	/**
	 *	Constructs a new cache that
	 * 	@return Cache
	 */
	public function __construct($enginename) {
		parent::__construct();
		$this->engine($enginename);
		return $this;
	}
	
	public function engine(CacheEngine $engine) {
		
	}
	
	public function read($name) {
		return $this->engine->read($name);
	}
	
	public function write($name, $data, $expire) {
		
	}
	
	public function garbageCollect() {
		
	}
	
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class CacheException extends BasicException {}



?>