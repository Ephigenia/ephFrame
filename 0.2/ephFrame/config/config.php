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
 * 	@package ephFrame
 * 	@subpackage ephFrame
 */

/**
 *	This file stores default ephFrame / Application constants, it's included
 * 	before the Apps config file located in /app/config.php.
 * 	Please use the {@link Registry} class to define new configuration vars. 
 */

Registry::set('DEBUG', DEBUG_PRODUCTION);

?>