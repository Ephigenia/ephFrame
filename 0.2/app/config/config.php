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

/**
 * 	!!CHANGE THIS AS SOON AS POSSIBLE!!
 * 	Salt for use in password creation or anything else that need so be salted
 */
define('SALT', 'priotaseloukeadotraeuocrailaejot');

/**
 * 	Session Name (also the name for the session cookie)
 * 	change this if you want to
 */
define('SESSION_NAME', 'app');

/**
 * 	TimeZone Setting
 * 	This Setting influences alle location based methods of php
 */ 
date_default_timezone_set('Europe/Berlin');

/**
 *	Optional default logging level, this is also affected by setting DEBUG,
 *	so you usually won’t need to set this yourself.
 */
// Log::$level = Log::INFO;

/**
 * 	Enable this to render the element names before element itsself,
 * 	debug.css must be included as well DEBUG > DEBUG_PRODUCTION
 */
// Registry::set('debug.showElementName', true);

/**
 *	Override DEBUGs level default error_reporting value by setting ERROR_REPORTING
 *	value to your specific value
 */
// ?XDEBUG_PROFILE=1

?>