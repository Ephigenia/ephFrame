<?php

/**
 * Application-Wide Configuration File
 *
 * This file is loaded right after the framework setup and bevor any controller
 * is loaded. You can completely overwrite and change configurationv vars here.
 *
 * @package app
 * @subpackage app.config
 */

/**
 * Set a new debug level when you're in production, you can assign
 * the debug level to different server adresses by using the
 * third argument for Registry::set, see {@link Registry}
 * (The Debug Level Constants are set in ephFrame/config/constants.php)
 */
Registry::set('DEBUG', DEBUG_VERBOSE);

/**
 * !!CHANGE THIS AS SOON AS POSSIBLE!!
 * Salt for use in password creation or anything else that need so be salted
 */
define('SALT', 'priotaseloukeadotraeuocrailaejot');

/**
 * Session Name (also the name for the session cookie)
 * change this if you need to
 */
Registry::set('Session.name', 'app');

/**
 * Override PHPs default session lifetime when sessions are saved in cookies
 */
Registry::set('Session.ttl', WEEK);

/**
 * Only used when the {@link I18NComponent} is used in the application. Change
 * this to your default language setting. See the internationalization-part
 * in the ephFrame documentation: {@todo add link here}
 */
Registry::set('I18n.language', 'de_DE');

/**
 * TimeZone Setting
 * This Setting influences alle location based methods of php
 */ 
date_default_timezone_set('Europe/Berlin');

/**
 * Optional default logging level, this is also affected by setting DEBUG,
 * so you usually won’t need to set this yourself.
 */
// Log::$level = Log::INFO;

/**
 * Enable this to render the element names before element itsself,
 * debug.css must be included as well DEBUG > DEBUG_PRODUCTION
 */
// Registry::set('debug.showElementName', true);
