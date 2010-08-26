<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

// PHP Version Check, ephFrame would need 5.1.6!
if ((int) str_pad(preg_replace('@[^\d]+@', '', phpversion()), 6, '0', STR_PAD_RIGHT) < 516000) {
	die (sprintf('The PHP version installed (%s) does not work with ephFrame. Minimum php version is 5.1.6', phpversion()));
}

define('DS', DIRECTORY_SEPARATOR);

// check for defined FRAME_ROOT
if (!defined('FRAME_ROOT')) {
	define('FRAME_ROOT', dirname(realpath(__FILE__)).'/');
}
if (ini_get('register_globals') === true) {
	die ('ephFrame will not work when register globals is enabled in php.ini.');
}

// unset all variables coming from global vars or whatever
foreach (get_defined_vars() as $varname) {
	unset($varname);
}

// disable magic quotes
if(function_exists('set_magic_quotes_runtime')) {
	@set_magic_quotes_runtime(FALSE);
}

// ephFrame Basic Stuff
if(!file_exists(FRAME_ROOT.'lib/core/ephFrame.php')) {
	die ('Unable to find ephFrame, please set FRAME_ROOT to the ephFrame directory.');
}
require FRAME_ROOT.'lib/core/ephFrame.php';
ephFrame::init();