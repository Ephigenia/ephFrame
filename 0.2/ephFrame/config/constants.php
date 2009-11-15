<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

/**
 * Set compile starting time, this used later for 
 * returning the hole compile time
 */
define('COMPILE_START', microtime(true));

/**
 * Different DEBUG States
 * you need to set the Debug level to 0 if you're in production, i.e. on
 * the deployed server
 * 
 * DEBUG_PRODUCTION
 * 	suspends every php error, mysql error and any kind of information about
 * 	any error is not displayed but logged
 * DEBUG_DEBUG
 * 	Append a db query log to every view
 * DEBUG_VERBOSE
 * 	writes more data to the verbose log
 */
define('DEBUG_PRODUCTION', 0);
define('DEBUG_DEVELOPMENT', 1);
define('DEBUG_DEBUG', 2);
define('DEBUG_VERBOSE', 3);

define('CLI_MODE', !isset($_SERVER['SERVER_PORT']));

/**
 * String literals, used by many classes
 */
define('TAB', chr(9));
define('LF', chr(10));
define('RT', chr(13));
define('RTLF', RT.LF);
define('ESC', chr(27));
define('BELL', chr(7));

/**
 * Some time constants that are helpfull for timestamp calculations,
 * don't use them to much, cause you might get stuck in a leap year!
 */
define('SECOND', 1);
define('MINUTE', 60 * SECOND);
define('HOUR', 60 * MINUTE);
define('DAY', 24 * HOUR);
define('WEEK', 7 * DAY);
define('MONTH', 30 * DAY);
define('YEAR', 365 * DAY);

/**
 * Some File Sizes
 */
define('BYTE', 1);
define('KILOBYTE', BYTE * 1024);
define('MEGABYTE', KILOBYTE * 1024);
define('GIGABYTE', MEGABYTE * 1024);
define('TERABYTE', GIGABYTE * 1024);
define('PENTABYTE', TERABYTE * 1024);
