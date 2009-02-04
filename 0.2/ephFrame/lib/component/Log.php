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

// load classes that are used
ephFrame::loadClass('ephFrame.lib.helper.ArrayHelper');

/**
 *	A class for logging messages to files
 * 
 * 	The log messages are stored in files, separeted by a simple line break (\n)
 * 	the files are located in APP_LOG directory which can be set in the
 * 	config.php file.<br />
 * 	You also can overwrite this class by extending it and create your own
 * 	logging logic.<br />
 * 	<br />
 * 	The class is designed to be a singleton, therefore it's accessible from every
 * 	php code within the framework and your app.<br />
 * 	<br />
 * 
 * 	Example usage in a controller:
 * 	<code>
 * 	class MyController extends AppController {
 * 		public $components = array('Log', 'AccessControl');
 * 		public function index() {
 * 			if ($this->AccessControl->validUser()) {
 * 				Log::write(Log::ERROR, 'invalid user detected');
 * 			}
 * 		}
 * 	}
 * 	</code>
 * 	<br />
 * 
 *	You also can use the shortcut function, defined in core.php:
 * 	<code>
 * 	logg(Log::ERROR, 'error found!');
 * 	</code>
 * 	<br />
 * 
 * 	If you wish to use your own file for app specific messages, pass a string
 * 	instead of an integer:
 * 	<code>
 * 	logg('app', 'user logged in'.$User->name);
 * 	</code>
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de
 * 	@since 27.12.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component
 * 	@version 0.1
 * 	@uses File
 * 	@uses String
 * 	@uses Charset
 * 	@uses ArrayHelper
 */
class Log extends Component {
	
	/**
	 * 	Base path were log files are stored
	 * 	@var string
	 */
	public static $path;
	
	/**
	 * 	Standarized english date format, that should be standard for all log
	 * 	files.
	 * 	@var string
	 */
	public static $dateFormat = 'Y-m-d H:i:s';
	
	/**
	 *	Log Message format
	 * 	// todo extract this to optional new class ?
	 * 	@var string
	 */
	public static $format = '%date% [%ip%] %message%';
	
	/**
	 *	Extension of log files
	 * 	@var string
	 */
	public static $extension = '.log';
	
	private static $instance;
	
	public static $level = self::INFO;
	
	/**
	 * 	@todo explain the different mechanics behind the levels
	 */
	const SILENCE = 0;
	const FATAL = 1;
	const ERROR = 2;
	const WARNING = 3;
	const NOTICE = 4;
	const DEBUG = 5;
	const INFO = 6;
	const VERBOSE = 7;
	const VERBOSE_SILENT = 8;
	
	/**
	 * 	Log Severity levels => filename
	 * 	@var array(string)
	 */
	public static $levels = array(
		 self::ERROR => 'error'
		,self::WARNING => 'warning'
		,self::NOTICE => 'notice'
		,self::DEBUG => 'debug'
		,self::INFO => 'info'
		,self::VERBOSE => 'verbose'
		,self::VERBOSE_SILENT => 'verbose'
	);
	
	public function __construct() {
		self::$path = LOG_DIR;
	}

	/**
	 * 	Returns an instance of Log
	 * 	@return Log
	 */
	public static function getInstance() {
  		if (!self::$instance) {
  			ephFrame::loadClass('ephFrame.lib.File');
  			$classname = __CLASS__;
  			self::$instance = new $classname();
  		}
  		return self::$instance;
  	}

  	/**
  	 *	Write a message to the log.
  	 * 
  	 * 	The Filename of the log depends on the $level you pass. If the file
  	 * 	doesn't exist it will be created.
  	 * 	
  	 * 	Arrays will be flattened and objects are tried to convert to a string
  	 * 	by using __toString or render().
  	 * 	All Log Messages are stripped from new line characters
  	 * 	<code>
  	 * 	// in the controller, add a message to the log
  	 * 	$this->Log(Log::INFO, 'So we tested this baby!');
  	 * 	</code>
  	 * 	@param integer $level
  	 * 	@message message that should be logged
  	 * 	@return boolean true on success
  	 */
	public static function write($level, $message) {
		// log message only if level is higher or equal current reporting level
		if ($level <= self::$level) {
			$log = self::getInstance();
			$logFile = new File(self::logFileName($level));
			$logFile->append($log->createLogMessage($message));
		}
		return true;
	}
	
	/**
	 * 	Formats the log message
	 *
	 * 	@param mixed $message
	 * 	@return string
	 */
	public function createLogMessage($message) {
		// render the mesage if object or array if neede
		switch(gettype($message)) {
			case 'array':
				$message = ArrayHelper::flatten($message);
				break;
			case 'object':
				if (method_exists($message, '__toString')) {
					$message = $message->__toString();
				} elseif(method_exists($message, 'render')) {
					$message = $message->render();
				}
				break;
			case 'resource':
				$message = 'ressourceid: \''.$message.'\' of type \''.get_resource_type($message).'\'';
				break;
		}
		// strip newlines
		$message = preg_replace('/[\n\r]+/', ' ', $message);
		// replace wildcards
		$message = String::substitute(self::$format.LF, array(
			'date' => date(self::$dateFormat),
			'ip' => (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown'),
			'message' => $message,
			'user' => get_current_user()
		));
		// return resulting message
		return $message;
	}
	
	/**
	 *	Stores the translated log file names as cache
	 * 	@var array(string)
	 */
	public static $logFilenames = array();
	
	/**
	 *	Translates the log level to a full path to the log filename and returns it
	 * 	This method takes care of invalid characters, spaces and empty levels.
	 * 	The filename is generally speaking created using the {@levels} array as 
	 * 	soon the level is known - otherwise a filename is created depending
	 * 	on the level name.
	 * 
	 * 	@param integer $level
	 * 	@return string
	 */
	public static function logFileName($level) {
		if (!isset(self::$logFilenames[$level])) {
			$path = realpath(self::$path).DS;
			$logFileBasename = '';
			if ($level == self::ERROR || $level == self::WARNING) {
				$logFileBaseName = 'error';
			} elseif (array_key_exists($level, self::$levels)) {
				$logFileBaseName = self::$levels[$level];
			} elseif (!empty($level)) {
				$logFileBasename = $level;
			}
			// cleanup the filename
			$logFileBasename = strtolower($logFileBasename);
			$logFileBasename = Charset::toSingleBytes($logFileBasename);
			// replace spaces
			$logFileBasename = preg_replace('/\s+/', '_', $logFileBasename);
			// replace any control character or non-word stuff
			$logFileBasename = preg_replace('/[^-_., \d\w]/u', '', $logFileBasename);
			// limit size of level name to unix type filename length
			$logFileBasename = substr($logFileBasename, 0, 255);
			if (empty($logFileBaseName)) {
				$logFileBasename = 'unknown_level';
			}
			// return the resulting path to the logfile
			self::$logFilenames[$level] = $path.$logFileBaseName.self::$extension;
		} 
		return self::$logFilenames[$level];
	}
	
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class LogException extends ComponentException {}

?>