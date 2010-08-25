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
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

class_exists('BasicException') or require dirname(__FILE__).'/BasicException.php';

/**
 * ephFrame
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 06.05.2007
 * @package ephFrame
 */
final class ephFrame
{	
	/**
	 * Stores the ephFrame version
	 * @var integer
	 */
	const VERSION = '0.3';
	
	/**
	 * Stores the instance of {@link ephFrame} as soon
	 * singleton is called
	 * @var ephFrame
	 */
	public static $instance;
	
	/**
	 * This is the first object method ever called in the framework
	 * initiates all important stuff and so on - ne wa!
	 */
	public static function init()
	{
		if (empty(self::$instance)) {
			require (FRAME_ROOT.'config/constants.php');
			require (FRAME_ROOT.'config/paths.php');
			require (FRAME_LIB_DIR.'core/Object.php');
			require (FRAME_ROOT.'core.php');
			require (FRAME_LIB_DIR.'core/PHPINI.php');
			require (FRAME_LIB_DIR.'core/Library.php');
			require (FRAME_LIB_DIR.'component/Component.php');
			require (FRAME_LIB_DIR.'util/Validator.php');
			require (FRAME_LIB_DIR.'util/String.php');
			require (FRAME_LIB_DIR.'util/Sanitizer.php');
			require (FRAME_LIB_DIR.'util/Registry.php');
			require (FRAME_ROOT.'config/config.php');
			self::loadEnvironmentConfig();
			class_exists('AppComponent') or require APP_LIB_DIR.'component/AppComponent.php';
			require (FRAME_LIB_DIR.'component/Log.php');
			class_exists('AppController') or require APP_LIB_DIR.'AppController.php';
			class_exists('AppModel') or require APP_LIB_DIR.'model/AppModel.php';
			if (file_exists(APP_LIB_DIR.'component/Form/AppForm.php') && !class_exists('AppForm')) {
				require APP_LIB_DIR.'component/Form/AppForm.php';
			}
			self::setErrorReporting();
			logg(Log::VERBOSE_SILENT, 'ephFrame: successfully loaded, now going to dispatcher');
		}
		return self::$instance;
	}
	
	public static function loadEnvironmentConfig()
	{
		$username = @get_current_user();
		if (empty($username) && isset($_ENV['USERNAME'])) {
			$username = $_ENV['username'];
		}
		$username = basename(trim(strtolower($username)));
		$configCascade = array(
			APP_ROOT.'config/config.php',
			APP_ROOT.'config/user/default.php'
		);
		// user configuration files
		if (!empty($username)) {
			$configCascade[] = APP_ROOT.'config/user/'.basename(strtolower(@get_current_user())).'.php';
			$configCascade[] = APP_ROOT.'config/user/'.basename(strtolower(@get_current_user())).'.db.php';
		}
		// host configurations
		$configCascade[] = APP_ROOT.'config/host/default.php';
		if (!empty($_SERVER['HTTP_HOST'])) {
			$configCascade[] = APP_ROOT.'config/host/'.basename(@$_SERVER['HTTP_HOST']).'.php';
			$configCascade[] = APP_ROOT.'config/host/'.basename(@$_SERVER['HTTP_HOST']).'.db.php';
		}
		foreach($configCascade as $filename) {
			if (file_exists($filename) && is_readable($filename)) require $filename;
		}
		if (!class_exists('DBConfig')) {
			include APP_ROOT.'config/db.php';
		}
		return true;
	}
	
	/**
	 * Set error reporting depending on DEBUG
	 * you can orverwrite current ERROR_REPORTING by setting a new
	 * ERROR_REPORTING in the applications config, but you can't do
	 * that if the DEBUG level is set to Production, then every
	 * error will be ignored
	 */
	public static function setErrorReporting()
	{
		if (Registry::get('DEBUG') > DEBUG_PRODUCTION) {
			if (Registry::get('DEBUG') == DEBUG_VERBOSE) {
				Log::$level = Log::VERBOSE;
			} elseif (Registry::get('DEBUG') > DEBUG_VERBOSE) {
				Log::$level = Log::VERBOSE_SILENT;
			}
			$error_reporting = Registry::get('ERROR_REPORTING');
			if (!is_null($error_reporting)) {
				error_reporting(Registry::get('ERROR_REPORTING'));
			} else {
				error_reporting(E_ALL + E_STRICT);
			}
			PHPINI::set('display_errors', 'yes');
			PHPINI::set('display_startup_errors', 'yes');
		} else {
			error_reporting(0);
		}
		return true;
	}
	
	/**
	 * Returns the compile time from starting php compilation to the time you
	 * call this function.
	 * @todo move this to any other helper class
	 * @param integer $precision
	 * @return float
	 */
	public static function compileTime($precision = 4)
	{
		return round(microtime(true) - COMPILE_START, $precision);
	}
	
	/**
	 * Tries to determine the current memory usage by PHP in bytes
	 * @todo move this to any other helper class
	 * @param boolean $humanized
	 * @return integer
	 */
	public static function memoryUsage()
	{
		$usage = 0;
		if (function_exists('memory_get_usage')) {
			$usage = memory_get_usage();
		} else if (substr(PHP_OS, 0, 3) == 'WIN') {
        	if (substr( PHP_OS, 0, 3) == 'WIN') { 
				$output = array(); 
				exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output); 
				$usage = preg_replace( '/[\D]/', '', $output[5] ) * 1024; 
			} 
		} else {
			$pid = getmypid(); 
			exec('ps -o rss -p '.$pid, $output);
			$usage = $output[1] * 1024;
		}
		return $usage;
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ephFrameException extends BasicException 
{}