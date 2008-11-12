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
 * 	Default Applications paths
 */
if (!defined('APP_ROOT')) {
	if (basename(getcwd()) == 'webroot') {
		define('APP_ROOT', '../');
	} else {
		define ('APP_ROOT', dirname(getcwd().'/a').'/');		// absolute path to application root
	}
}
if (!defined('CONFIG_DIR')) define('CONFIG_DIR', APP_ROOT.'config/');
if (!defined('VIEW_DIR')) define ('VIEW_DIR', APP_ROOT.'view/');
if (!defined('ELEMENTS_DIR')) define ('ELEMENTS_DIR', VIEW_DIR.'element/');
if (!defined('LAYOUT_DIR')) define ('LAYOUT_DIR', VIEW_DIR.'layout/');
if (!defined('TMP_DIR')) define ('TMP_DIR', APP_ROOT.'tmp/');
if (!defined('LOG_DIR')) define ('LOG_DIR', TMP_DIR.'log/');
if (!defined('MODELCACHE_DIR')) define ('MODELCACHE_DIR', TMP_DIR.'model/');

if (!defined('WEBROOT')) {
//	echo '<pre>';
//	echo $_SERVER['DOCUMENT_ROOT'].LF;
//	echo realpath(APP_ROOT).LF;
//	exit;
	
	if (preg_match('/^'.preg_quote($_SERVER['DOCUMENT_ROOT'], '/').'/', realpath(APP_ROOT).'/')) {
		$__webroot = str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(APP_ROOT).'/');
		if (empty($__webroot)) {
			$__webroot = '/';
		} elseif (strlen($__webroot) > 1 && substr($__webroot, 0, 1) !== '/') {
			$__webroot = '/'.$__webroot;
		}
	} else {
//		$__webroot = '/';
		$__webroot = str_repeat('../', substr_count($_SERVER['REQUEST_URI'], '/')-2);
	}
	define ('WEBROOT', $__webroot); // absolute path to webroot
	unset($__webroot);
}

?>