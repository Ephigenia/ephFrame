<?php

/**
 * This file loads console tasks by it’s name, call it from the application
 * root directory like this:
 *
 * $ php console/console.php cronReportEmail 
 * 
 * @since 2009-09-28
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @package app
 * @subpackage app.console
 */

// load ephFrame Framework
require (dirname(__FILE__).'/../html/ephFrame.php');
loadClass('ephFrame.lib.ConsoleController');

// get desired job name from console argument list
$jobName = preg_replace('@[^A-Z0-9a-z_-]@i', '', @$argv[1]);
if (empty($jobName)) die('No Job name specified.'.LF);

// check if job file is there and readable
$jobFilename = dirname(__FILE__).'/'.basename($jobName).'.php';
if (!is_file($jobFilename)) die(sprintf('Unable to find job file: %s', $jobFilename).LF);

// load job and run it
require $jobFilename;
$jobClassname = Inflector::camellize($jobName, true).'Controller';
$job = new $jobClassname(new HTTPRequest());