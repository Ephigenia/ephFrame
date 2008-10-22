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

ephFrame::loadClass('ephFrame.lib.component.Compressor');

/**
 * 	Class that compresses/packes Javascripts
 * 
 * 	@author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * 	@since 12.05.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component
 */
class JSCompressor extends Compressor {
	
	/**
	 *	Compresses the passes javascript code
	 * 	@todo implement a real compression algorithm for javascript, that is still missing
	 * 	@param string $code
	 *	@return string
	 */
	public function compress($code) {
		// strip multiline comments
		$code = String::stripComments($code);
		return $code;
	}
	
	public function basicCompress($code) {
		return $code;
	}
	
}
?>