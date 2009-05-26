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

/**
 * 	ephFrame mother class
 * 
 * 	use this class for loading new classes
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 06.05.2007
 * 	@package ephFrame
 */
final class ephFrame {
	
	/**
	 *	Stores the ephFrame version
	 * 	@var integer
	 */
	const VERSION = '0.2a';
	
	/**
	 *	Stores the instance of {@link ephFrame} as soon
	 * 	singleton is called
	 * 	@var ephFrame
	 */
	public static $instance;
	
	/**
	 *	This is the first object method ever called in the framework
	 * 	initiates all important stuff and so on - ne wa!
	 */
	public static function singleton() {
		if (empty(self::$instance)) {
			self::checkRegisterGlobals();
			require (FRAME_ROOT.'config/constants.php');
			require (FRAME_ROOT.'config/paths.php');
			require (FRAME_ROOT.'Object.php');
			require (FRAME_ROOT.'core.php');
			require (FRAME_LIB_DIR.'PHPINI.php');
			require (FRAME_LIB_DIR.'component/Component.php');
			require (FRAME_LIB_DIR.'ClassPath.php');
			require (FRAME_HELPERS_DIR.'Helper.php');
			require (FRAME_HELPERS_DIR.'Validator.php');
			require (FRAME_HELPERS_DIR.'String.php');
			require (FRAME_LIB_DIR.'helper/Sanitizer.php');
			require (FRAME_LIB_DIR.'exception/IntrusionException.php');
			require (FRAME_LIB_DIR.'exception/TypeException.php');
			require (FRAME_LIB_DIR.'Registry.php');
			require (FRAME_ROOT.'config/config.php');
			require (FRAME_LIB_DIR.'Renderable.php');
			require (FRAME_LIB_DIR.'component/Log.php');
			// project include stuff (some config variables can be overwritten there)
			include (APP_ROOT.'config/config.php');
			include (APP_ROOT.'config/db.php');
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
	
	/**
	 *	Checks for register global initate set in the php config
	 * 	if this is on the app will grump you for that :D
	 */
	public static function checkRegisterGlobals() {
		if (ini_get('register_globals') === true) {
			die ('ephFrame will not work when register globals is enabled in php.ini.');
		}
	}
	
	/**
	 *	Set error reporting depending on DEBUG
	 * 	you can orverwrite current ERROR_REPORTING by setting a new
	 * 	ERROR_REPORTING in the applications config, but you can't do
	 * 	that if the DEBUG level is set to Production, then every
	 * 	error will be ignored
	 */
	public static function setErrorReporting() {
		if (Registry::defined('ERROR_REPORTING') && Registry::get('DEBUG') > DEBUG_PRODUCTION) {
			error_reporting(Registry::get('ERROR_REPORTING'));
		} elseif (Registry::defined('DEBUG')) {
			if (Registry::get('DEBUG') > DEBUG_PRODUCTION) {
				error_reporting(E_ALL + E_STRICT);
				PHPINI::set('display_errors', 'yes');
				PHPINI::set('display_startup_errors', 'yes');
			} else if (Registry::get('DEBUG') == DEBUG_PRODUCTION) {
				error_reporting(0);
			}
		}
	}
	
	/**
	 *	Loads a component from ephFrame or the application
	 * 	@param string $componentName
	 * 	@return boolean
	 */
	public static function loadComponent($componentName) {
		$className = ucFirst($componentName);
		if (ClassPath::exists('ephFrame.lib.component.'.$className)) {
			loadClass('ephFrame.lib.component.'.$className);
		} elseif (ClassPath::exists('app.lib.component.'.$className)) {
			loadClass('app.lib.component.'.$className);
		} else {
			logg(Log::VERBOSE_SILENT, 'ephFrame: failed loading component \''.$componentName.'\'');
			return false;
		}
		return true;
	}
	
	/**
	 *	Loads a helper class from ephFrame lib or application lib
	 * 	@param string $helperName
	 * 	@return boolean
	 */
	public static function loadHelper($helperName) {
		$className = ucFirst($helperName);
		if (ClassPath::exists('ephFrame.lib.helper.'.$className)) {
			loadClass('ephFrame.lib.helper.'.$className);
		} elseif (ClassPath::exists('app.lib.helper.'.$className)) {
			loadClass('app.lib.helper.'.$className);
		} else {
			logg(Log::VERBOSE_SILENT, 'ephFrame: failed loading helper \''.$helperName.'\'');
			return false;
		}
		return true;
	}
	
	
	/**
	 * 	Loads a class, syntax is like in flash or javascript applications:
	 * 	<code>
	 * 		// load XML Component Class
	 * 		ephFrame::loadClass("ephFrame.lib.component.XML");
	 * 		// load a class within the application
	 * 		ephFrame::loadClass("app.lib.ownClass");
	 * 	</code>
	 * 
	 * 	It't not possible to load classes that are out of the document root by
	 * 	checking for ../ oder / or all / characters
	 *	
	 * 	@throws ephFrameMalFormedClassPathException if classPath is malformed
	 * 	@param string $classPath
	 * 	@return true
	 */
	public static function loadClass($classPath) {
		if (empty($classPath)) throw new StringExpectedException();
		$className = ClassPath::className($classPath);
		if (!class_exists($className)) {
			self::loadFrameWorkFile($classPath);
		}
		return $className;
	}
	
	/**
	 *	Loads a interface. This is just like {@link loadClass}
	 * 	but testing if the interface really was loaded afterwards
	 * 	
	 * 	@return boolean
	 * 	@throws ephFrameInterfaceFileFoundButNotLoadedException
	 */
	public static function loadInterface($interfacePath) {
		if (empty($interfacePath)) throw new StringExpectedException();
		$interfaceName = ClassPath::className($interfacePath);
		if (!interface_exists($interfaceName)) {
			self::loadFrameWorkFile($interfacePath);
		}
		return true;
	}
	
	/**
	 * 	Loads a FrameWorkFile
	 * 	@param string $path
	 * 	@throws ephFrameClassFileNotFoundException
	 * 	@return string	loaded filename class or interface name
	 */
	public static function loadFrameWorkFile($path) {
		$translatedPath = ClassPath::translatePath($path);
		if (!ClassPath::exists($path)) throw new ephFrameClassFileNotFoundException($path);
		require $translatedPath;
		return true;
	}
	
	/**
	 *	Returns the compile time from starting php compilation to the time you call this function
	 *	@param	integer	$precision
	 *	@return float
	 */
	public static function compileTime($precision = 4) {
		return round(microtime(true) - COMPILE_START, $precision);
	}
	
	/**
	 * 	Tries to determine the current memory usage by PHP in bytes
	 * 	@param boolean $humanized
	 * 	@return integer
	 */
	public static function memoryUsage($humanized = false) {
		$usage = 0;
		if (function_exists('memory_get_usage')) {
			$usage = memory_get_usage();
		} else if ( substr(PHP_OS,0,3) == 'WIN') {
        	if ( substr( PHP_OS, 0, 3 ) == 'WIN' ) { 
				$output = array(); 
				exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output); 
				$usage = preg_replace( '/[\D]/', '', $output[5] ) * 1024; 
			} 
		} else {
			$pid = getmypid(); 
			exec('ps -o rss -p '.$pid, $output);
			$usage = $output[1] * 1024;
		}
		if ($humanized) {
			ephFrame::loadClass('ephFrame.lib.File');
			return File::sizeHumanized($usage);
		} else {
			return $usage;
		}
	}
	
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ephFrameException extends BasicException {
	public $level = BasicException::FATAL;
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ephFrameLoadError extends ephFrameException {
	
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ephFrameClassFileNotFoundException extends ephFrameLoadError {
	public function __construct($classPath) {
		if (empty($classPath)) {
			$this->message = 'No Classpath given.';
		} else {
			/*
			if (strlen($classPath) > 30) {
				$classPath = '...'.substr($classPath, strlen($classPath) - 30);
			}*/
			$this->message = 'Sorry i was unable to find the class file \''.$classPath.'\'';
		}
		parent::__construct($this->message);
	}
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ephFrameInterfaceFileFoundButNotLoadedException extends ephFrameException {
	public function __construct($interfaceName, $path) {
		$this->message = 'Sorry, after loading Interface File \''.$path.'\' interface named \''.$interfaceName.'\' was not found.';
	}
}

?>