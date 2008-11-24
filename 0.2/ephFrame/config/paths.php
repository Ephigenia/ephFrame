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
 * 	@package ephFrame
 * 	@subpackage ephFrame
 */

/**
 * 	This file stores some paths that are used by the application and ephframe.
 * 	Please make sure that every path constant value ends with an '/'.
 */

/**
 * 	ephFrame internal paths
 */
define ('FRAME_LIB_DIR', FRAME_ROOT.'lib/');
define ('FRAME_COMPONENT_DIR', FRAME_LIB_DIR.'component/');
define ('FRAME_HELPERS_DIR', FRAME_LIB_DIR.'helper/');

/**
 * 	Path to vendor files, classes that come from third parties
 */
define ('VENDOR_ROOT', FRAME_ROOT.'../vendor/');

/**
 * 	Absolute Path to application root, always one up before webroot/ or html!
 */
if (!defined('APP_ROOT')) {
	define('APP_ROOT', realpath(getcwd().'/../').'/');
}
if (!defined('CONFIG_DIR')) define('CONFIG_DIR', APP_ROOT.'config/');
if (!defined('VIEW_DIR')) define ('VIEW_DIR', APP_ROOT.'view/');
if (!defined('ELEMENTS_DIR')) define ('ELEMENTS_DIR', VIEW_DIR.'element/');
if (!defined('LAYOUT_DIR')) define ('LAYOUT_DIR', VIEW_DIR.'layout/');
if (!defined('TMP_DIR')) define ('TMP_DIR', APP_ROOT.'tmp/');
if (!defined('LOG_DIR')) define ('LOG_DIR', TMP_DIR.'log/');
if (!defined('MODELCACHE_DIR')) define ('MODELCACHE_DIR', TMP_DIR.'model/');

/**
 *	Determine webroot, the directory that can be used for linking images
 * 	files relative on the server
 */
if (!defined('WEBROOT')) {
	
	$debug = false;
	if (!empty($debug)) {
		echo '<pre>';
		echo 'APP_ROOT '.TAB.TAB.APP_ROOT.LF;
		echo 'APP_ROOT basename: '.TAB.basename(getcwd()).LF;
		echo 'APP_ROOT webroot:  '.TAB.APP_ROOT.basename(getcwd()).LF;
		echo 'APP_ROOT realpath: '.TAB.realpath(APP_ROOT).LF;
		echo 'DOC_ROOT'.TAB.TAB.$_SERVER['DOCUMENT_ROOT'].LF;
		echo 'DOC_ROOT_REAL'.TAB.TAB.realpath($_SERVER['DOCUMENT_ROOT']).LF;
		echo 'REQUEST_URI'.TAB.TAB.$_SERVER['REQUEST_URI'].LF.LF;	
		echo '</pre>';
	}
	
	$__webroot = str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', APP_ROOT);
	if ($__webroot == APP_ROOT) {
		$__webroot = '/';
	}
	
	// try to find actual webroot directory (most cases: html, webroot in real doc root)
	
	/*
	if (preg_match('/^'.preg_quote($_SERVER['DOCUMENT_ROOT'], '/').'/', realpath(APP_ROOT).'/')) {
		$__webroot = str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(APP_ROOT).'/');
		if (empty($__webroot)) {
			$__webroot = '/';
		} elseif (strlen($__webroot) > 1 && substr($__webroot, 0, 1) !== '/') {
			$__webroot = '/'.$__webroot;
		}
	} else {
		$depth = 0;
		$requestUri = trim($_SERVER['REQUEST_URI'], '/');
		$appRootLastDirName = basename(realpath(APP_ROOT));
		if ($requestUri = $appRootLastDirName) {
			$__webroot = '/'.$appRootLastDirName.'/';
		} else {
			$requestPathArr = explode('/', dirname(preg_replace('/^([^?]*)(\?(.*))?$/', '\\1', $requestUri)));
			for($i = count($requestPathArr)-1; $i > 0; $i--) {
	 			if ($requestPathArr[i] === $appRootLastDirName) {
					break;
				} else {
					$depth++;
				}
			}
			$__webroot = str_repeat('../', $depth);
		}
	}
	*/
	
	if (!empty($debug)) {
		var_dumP($__webroot);
		exit;
	}
	define ('WEBROOT', $__webroot); // relative path to webroot (from the clients perspective)
	unset($__webroot);
}

?>