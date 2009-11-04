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

class_exists('File') or require dirname(__FILE__).'/../File.php';

/**
 * Abstract Compressor
 * 
 * Compress stuff, files & strings
 * 
 * @author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * @since 01.06.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @uses File
 */
abstract class Compressor extends AppComponent {
	
	/**
	 * Compresses the passes string
	 * @param string $code
	 * @return string
	 */
	public function compress($string) {
		return $string;
	}
	
	/**
	 * Compresses the content of a css file and returns the compressed
	 * content of the file. If the file does not exists and exception is thrown
	 * @param string $filename
	 * @return string
	 */
	public function compressFile($filename) {
		$file = new File($filename);
		return $this->compress($file->slurp());
	}
	
}