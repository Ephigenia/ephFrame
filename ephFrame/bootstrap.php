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

// Check for minimum required PHP Version
if ((int) str_pad(preg_replace('@[^\d]+@', '', phpversion()), 6, '0', STR_PAD_RIGHT) < 516000) {
	die (sprintf('The PHP version installed (%s) does not work with ephFrame. Minimum php version is 5.1.6', phpversion()));
}
if (ini_get('register_globals') === true) {
	die ('ephFrame will not work when register globals is enabled in php.ini.');
}
if (function_exists('set_magic_quotes_runtime')) {
	@set_magic_quotes_runtime(FALSE);
}
// check for defined FRAME_ROOT
if (!defined('EPHFRAME_ROOT')) {
	define('EPHFRAME_ROOT', realpath(__DIR__).'/');
}
if(!is_file(EPHFRAME_ROOT.'lib/core/ephFrame.php')) {
	die ('Unable to find ephFrame, please set EPHFRAME_ROOT to the ephFrame directory.');
}
require EPHFRAME_ROOT.'lib/core/ephFrame.php';
ephFrame::init();