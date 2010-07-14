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
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright	copyright 2007+, Ephigenia M. Eichner
 * @link		http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */


// change this path to simpletest, if you have it somewhere else
if (!defined('SIMPLE_TEST')) {
	define('SIMPLE_TEST', realpath(dirname(__FILE__).'/../vendor/simpletest/').'/');
}
if (!defined('FRAME_ROOT')) {
	define('FRAME_ROOT', realpath(dirname(__FILE__).'/../ephFrame/').'/');
}
if (!defined('APP_ROOT')) {
	define('APP_ROOT', realpath(dirname(__FILE__).'/../app/').'/');
}
