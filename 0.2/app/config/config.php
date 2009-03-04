<?php

/**
 *	This file is included after all ephFrame configuration vars
 * 	are set, you can modify configuration variables by overwriting
 * 	them.
 * 	@package app
 * 	@subpackage app.config
 */

/**
 * 	Set a new debug level when you're in production, you can assign
 * 	the debug level to different server adresses by using the
 * 	third argument for Registry::set, see {@link Registry}
 * 	(The Debug Level Constants are set in ephFrame/config/constants.php)
 */
Registry::set('DEBUG', DEBUG_VERBOSE);

/**
 * 	Session Name (also the name for the session cookie)
 */
define('SESSION_NAME', 'app');

/**
 *	Logging Level
 * 	This should normally set to a low level
 */
Log::$level = Log::INFO;

/**
 * 	Salt for use in password creation or anything else that need so be salted
 * 	!!CHANGE THIS AS SOON AS POSSIBLE!!
 */
define('SALT', 'priotaseloukeadotraeuocrailaejot');

/**
 * 	TimeZone Setting
 * 	This Setting influences alle location based methods of php
 */ 
date_default_timezone_set('Europe/Berlin');

/**
 *	Override DEBUGs level default error_reporting value by setting ERROR_REPORTING
 *	value to your specific value
 */
// Registry::set('ERROR_REPORTING', E_ALL);
// ?XDEBUG_PROFILE=1


?>