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

/**
 * 	interface for string filters
 * 
 * 	This class is used for every class that does filtering on strings.
 * 
 *	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de
 * 	@since 18.03.2008
 */
interface StringFilter {
	
	public function apply($string);
	
}

?>