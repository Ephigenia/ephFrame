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
 * 	Application-wide paths
 * 	@package app
 * 	@subpackage app.config
 */

if (!defined('STATIC_DIR')) define ('STATIC_DIR', 'static'.DS);
if (!defined('APP_LIB_DIR')) define ('APP_LIB_DIR', APP_ROOT.'lib/');
if (!defined('APP_CONTROLLER_DIR')) define('APP_CONTROLLER_DIR', APP_LIB_DIR.'controller/');

?>