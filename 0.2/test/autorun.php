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
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright	copyright 2007+, Ephigenia M. Eichner
 * @link		http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @filesource
 */

error_reporting(E_ALL & ~E_DEPRECATED);
require_once dirname(__FILE__).'/config.php';

if (!file_exists(SIMPLE_TEST.'autorun.php')) {
	die('Error: Unable to find Simpletest autorun file. Please install Simpletest into \''.SIMPLE_TEST.'\' directory'."\n");
}

// include simple test libs and fire them up
require_once SIMPLE_TEST.'autorun.php';
require_once SIMPLE_TEST.'reporter.php';

// go for a ephframe centrict tests... this is realy dirty
// but i got no idea how to do this in a cool DRY way
if (!class_exists('ephFrame')) {
	require (dirname(__FILE__).'/../ephFrame/'.'startup.php');
}
error_reporting(E_ALL);

// create a reporter, testing if we're in a server enviorenment
// and create a text reporter if we're not on a server
// called in a console/terminal enviorenment we go for the text
// reporter
if (isset($_SERVER['SERVER_ADDR'])) {
	header('Content-Type: text/html; charset=utf-8');
	class ephFrameTestHTMLReporter extends HtmlReporter {
		public function _getCss() {
			return parent::_getCss().'
			body, html {
				font-family: Pragmata, Monaco, Courier;
				margin: 0.6em 1em;
				font-size: 100.01%;
				line-height: 1.5em;
			}
			';
		}
	}
	$reporter = new ephFrameTestHTMLReporter('utf-8');
} else {
	$reporter = new TextReporter();
}