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
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

class_exists('Compressor') or require dirname(__FILE__).'/Compressor.php';

/**
 * GZip Compress
 * 
 * This will compress strings and files to GZip, if gzip is available.
 * 
 * This class tries to use the php gzipcompres method, but if it's not
 * available the class will compress nothing. Maybe you can extend the class
 * later to use to use some command line tool to compress stuff.
 * 
 * @author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * @since 22.05.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 */
class GZipCompressor extends Compressor {
	
	public $gZipAvailable = false;
	
	/**
	 * Level of compression 0-9 valid, 0 = off
	 * @var integer
	 */
	public $level = 9;
	
	public function __construct() {
		// check if gzip compression can be done?
		if (function_exists('gzcompress')) {
			$this->gZipAvailable = true;
		} else {
			$this->gZipAvailable = false;
		}
		return $this;
	}
	
	/**
	 * Compress a string and return the compressed string, withouth gzip header
	 * @param string $string
	 * @return string
	 */
	public function compress($string, $level = null) {
		if (!$this->gZipAvailable || !is_string($string)) {
			return $string;
		}
		return gzencode($string, $level !== null ? $level : $this->level, FORCE_GZIP);
	}
	
	/**
	 * Compress a file $filename and return the compressed content of the file
	 * or write the contents of the file to $target.
	 *
	 * @param string $filename
	 * @param string $target Optional target name file
	 * @return string|boolean
	 * @todo add gzip-compressFile
	 */
	public function compressFile($filename, $target = null) {
		
	}
	
}