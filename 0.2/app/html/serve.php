<?php
/**
 * This some kind of media serving proxy, you request a file and i'll check
 * if the file is there and not an attack and serve the file with expire headers
 * and gzip compression if available.
 * The aim of this file is to decrease the http/yslow score.
 */

/**
 * Configuration
 * expiration times in seconds per filename regular expression
 */
$conf = array(
	'\.(ico|gif|jpg|jpeg|png|swf|pdf|mov|mpg|mp3|wmv|ppt|csv|flv)$' => array(
		'ttl' => 3600 * 24 * 30,
		'gzip' => false
	),
	'\.(css|js|htc)$' => array(
		'ttl' => 3600 * 24 * 7,
		'gzip' => true
	)
);

/**
 * Main Script
 */
if (!(isset($_GET['url']) && $fileToServe = preg_replace('/[^-\/_ ~+\.A-Za-z0-9]/', '', trim(rawurldecode($_GET['url']))))) {
	exit;
} elseif(preg_match('/\.{1,}\//', $fileToServe) || empty($fileToServe) || !file_exists($fileToServe)) {
	exit;
}

if (isset($_GET['disableExpire'])) {
	$disableExpire = true;
}

// load mime types lib
define('FRAME_LIBS', '../../ephFrame/libs/');
require (FRAME_LIBS.'exceptions/BasicException.php');
require (FRAME_LIBS.'Object.php');
require (FRAME_LIBS.'helpers/Helper.php');
require (FRAME_LIBS.'helpers/MimeTypes.php');

// determine if gzip compression available
if (!empty($_SERVER['HTTP_ACCEPT_ENCODING'])
	&& preg_match('/gzip/i', $_SERVER['HTTP_ACCEPT_ENCODING'])
	&& function_exists('gzcompress')) {
	$gzipAvailable = true;
}

// mime type header
if ($mimeType = MimeTypes::mimeType($fileToServe)) {
	header('Content-Type: '.$mimeType);
}
foreach($conf as $fileRegexp => $data) {
	if (preg_match('/'.$fileRegexp.'/i', $fileToServe)) {
		// cache Control, http 1.0 / 1.1
		if (isset($data['ttl']) && empty($disableExpire)) {
			header('Cache-Control: max-age='.$data['ttl']);
			header('Expires: '.gmdate('D, d M Y H:i:s', time() + $data['ttl']).' GMT');
		} 
		// gzipped output
		if ($gzipAvailable && isset($data['gzip']) && $data['gzip'] == true) {
			header('Content-Encoding: gzip');
			die(gzencode(file_get_contents($fileToServe), 9, FORCE_GZIP));
		} else {
			readfile($fileToServe);
			exit;
		}
	}
}
readfile($fileToServe);