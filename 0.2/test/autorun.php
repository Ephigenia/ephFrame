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

require_once dirname(__FILE__).'/config.php';

// include simple test libs and fire them up
require_once SIMPLE_TEST.'autorun.php';
require_once SIMPLE_TEST.'reporter.php';

// define FRAME_ROOT if not defined yet
if (!defined('FRAME_ROOT')) {
	define('FRAME_ROOT', dirname(__FILE__).'/../ephFrame/');
}

// go for a ephframe centrict tests... this is realy dirty
// but i got no idea how to do this in a cool DRY way
if (!class_exists('ephFrame')) {
	require (FRAME_ROOT.'startup.php');
	error_reporting(E_ALL);
}

// create a reporter, testing if we're in a server enviorenment
// and create a text reporter if we're not on a server
// called in a console/terminal enviorenment we go for the text
// reporter
if (isset($_SERVER['SERVER_ADDR'])) {
	$reporter = new HtmlReporter();
} else {
	$reporter = new TextReporter();
}

?>