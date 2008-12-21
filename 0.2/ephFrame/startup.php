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
	
// PHP Version Check, ephFrame would need 5.1.6!
if ((int) str_pad(preg_replace('@[^\d]+@', '', phpversion()), 6, '0', STR_PAD_RIGHT) < 516000) { 
	die ('The php version installed ('.phpversion().') does not work with ephFrame. Minimum php version is 5.1.6');
}

define('DS', DIRECTORY_SEPARATOR);

// check for defined FRAME_ROOT
if (!defined('FRAME_ROOT')) {
	define('FRAME_ROOT', dirname(realpath(__FILE__)).'/');
}
// find ephFrame.php in the ephFrame lib dir, if fail die
if(!file_exists(FRAME_ROOT.'ephFrame.php')) {
	die ('Unable to find ephFrame, please set FRAME_ROOT to the ephFrame directory.');
}

// unset all variables coming from global vars or whatever
foreach (get_defined_vars() as $varname) {
	unset($varname);
}

// disable magic quotes
if(function_exists('set_magic_quotes_runtime')) {
    set_magic_quotes_runtime(FALSE);
}

// ephFrame Basic Stuff
require FRAME_ROOT.'lib/exception/BasicException.php';
require FRAME_ROOT.'ephFrame.php';
ephFrame::singleton();
	
?>