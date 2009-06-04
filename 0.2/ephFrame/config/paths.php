<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright (c) 2007+, Ephigenia M. Eichner
 * 						 Kopernikusstr. 8
 * 						 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 
 * 	@license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright		copyright 2007+, Ephigenia M. Eichner
 * 	@link			http://code.ephigenia.de/projects/ephFrame/
 * 	@version		$Revision$
 * 	@modifiedby		$LastChangedBy$
 * 	@lastmodified	$Date$
 * 	@filesource		$HeadURL$
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
 * 	Try to determine application root directory from current working
 * 	directory. Usually 'html'.
 */
if (!defined('APP_ROOT')) {
	if (!CLI_MODE && basename(getcwd()) == 'html') {
		define('APP_ROOT', realpath(getcwd().'/../').'/');
	} else {
		define('APP_ROOT', realpath(getcwd().'/').'/');
	}
}

if (!defined('CONFIG_DIR')) define('CONFIG_DIR', APP_ROOT.'config/');
if (file_exists(CONFIG_DIR.'paths.php')) require_once CONFIG_DIR.'paths.php';

if (!defined('APP_LIB_DIR')) define('APP_LIB_DIR', APP_ROOT.'lib/');
if (!defined('VIEW_DIR')) define ('VIEW_DIR', APP_ROOT.'view/');
if (!defined('TMP_DIR')) define ('TMP_DIR', APP_ROOT.'tmp/');
if (!defined('LOG_DIR')) define ('LOG_DIR', TMP_DIR.'log/');
if (!defined('CACHE_DIR')) define ('CACHE_DIR', TMP_DIR.'cache/');
if (!defined('MODELCACHE_DIR')) define ('MODELCACHE_DIR', TMP_DIR.'model/');

/**
 *	Determine webroot, the directory that can be used for linking images
 * 	files relative on the server
 */
if (!defined('WEBROOT')) {
	if (isset($_SERVER['PHP_SELF'])) {
		$__webroot = rtrim(dirname(dirname($_SERVER['PHP_SELF']).'../'), '/').'/';
	} else {
		$__webroot = str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', APP_ROOT);
	}
	if ($__webroot == APP_ROOT) {
		$__webroot = '/';
	}
	define ('WEBROOT', $__webroot); // relative path to webroot (from the clients perspective)
	unset($__webroot);
}

if (!defined('STATIC_DIR')) define('STATIC_DIR', 'static/');

?>