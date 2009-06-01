<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright (c) 2007+, Ephigenia M. Eichner
 * 						 Kopernikusstr. 8
 * 						 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 
 * 	@license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright		copyright 2007+, Ephigenia M. Eichner
 * 	@link			http://code.ephigenia.de/projects/ephFrame/
 * 	@version		$Revision$
 * 	@modifiedby		$LastChangedBy$
 * 	@lastmodified	$Date$
 * 	@filesource		$HeadURL$
 */

/**
 *	APC (Alternative PHP Cache) Caching engine
 * 
 * 	APC is very cool, because you save a lot time by not searching for
 * 	caching files, beacause all cache data is stored in the memory of 
 * 	the webserver. Read more about APC in the {@link http://www.php.net/apc APC}
 * 
 * 	// todo finish APC Cache Class
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 25.09.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class APC extends Object implements CacheEngine {
	
	public static function checkForAPCPECL() {
		
	}
	
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class APCException extends CacheException {}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class APCPECLNotFoundException extends APCException {}

?>