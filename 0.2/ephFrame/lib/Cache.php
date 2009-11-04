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
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

interface_exists('CacheEngine') or require dirname(__FILE__).'/CacheEngine.php';

/**
 * Cache
 * 
 * Create a file cache engine and store some data:
 * <code>
 * $fileCache = new Cache('file');
 * </code>
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 25.09.2007
 * @version 0.1
 * @package ephFrame
 * @subpackage ephFrame.lib
 */
class Cache extends Object {
	
	/**
	 * Stores instance of cache writers
	 * @var CacheEngine
	 */
	public $writer;
	
	public $defaultExpire = HOUR;
	
	/**
	 * create a new cache
	 * @return Cache
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
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class CacheException extends BasicException {}
