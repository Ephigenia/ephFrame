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
Registry::set('DEBUG', DEBUG_DEBUG);

// default session name
define('SESSION_NAME', 'app');

/**
 *	Logging Level
 * 	This should normally set to a low level
 */
Log::$level = Log::INFO;

/**
 * 	Salt for use in password creation or anything else that need so be salted
 * 	change this as soon as you can to increase security!
 */
define('SALT', 'priotaseloukeadotraeuocrailaejot');

/**
 * 	TimeZone Setting
 * 	This Setting influences alle location based methods of php
 */ 
date_default_timezone_set('Europe/Berlin');

// ?XDEBUG_PROFILE=1

/**
 *	Custom error reporting level, standard is E_ALL + E_STRICT if you 
 * 	set DEBUG to > DEBUG_PRODUCITON
 * 	But you can turn off E_STRICT for example by setting a new ERROR_REPORTING
 */
// Registry::set('ERROR_REPORTING', E_ALL);


?>