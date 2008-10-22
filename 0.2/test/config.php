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


// change this path to simpletest, if you have it somewhere else
if (!defined('SIMPLE_TEST')) {
	define('SIMPLE_TEST', dirname(__FILE__).'/../vendor/simpletest/');
}
if (!defined('FRAME_ROOT')) {
	define('FRAME_ROOT', dirname(__FILE__).'/../ephFrame/');
}
if (!defined('APP_ROOT')) {
	define('APP_ROOT', dirname(__FILE__).'/../app/');
}

?>