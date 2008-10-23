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
 * 	before the Apps config file located in /app/config.php. So you can overwrite
 * 	all constants created here in you application config.
 * 	
 * 	Please use the {@link Registry} class to define new configuration vars. 
 */

Registry::set('DEBUG', DEBUG_PRODUCTION);

/**
 * 	Registers a absolute URL to the root of this application for use in the 
 * 	application. You can modify this in the applications config to your own
 * 	preferred url. This is only available if not in cli-php.
 */
if (isset($_SERVER['HTTP_HOST'])) {
	Registry::set('WEBROOT_ABS', ($_SERVER['SERVER_PORT'] == 80 ? 'http' : $_SERVER['SERVER_PORT']).'://'.$_SERVER['HTTP_HOST'].WEBROOT);
}

?>