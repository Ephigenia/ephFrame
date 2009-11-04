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
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

// load classes that are used
class_exists('ArrayHelper') or require dirname(__FILE__).'/../helper/ArrayHelper.php';
class_exists('File') or require dirname(__FILE__).'/../File.php';

/**
 * A class for logging messages to files
 * 
 * The log messages are stored in files, separeted by a simple line break (\n)
 * the files are located in APP_LOG directory which can be set in the
 * config.php file.<br />
 * You also can overwrite this class by extending it and create your own
 * logging logic.<br />
 * <br />
 * The class is designed to be a singleton, therefore it's accessible from every
 * php code within the framework and your app.<br />
 * <br />
 * 
 * Example usage in a controller:
 * <code>
 * class MyController extends AppController {
 * 	public $components = array('Log', 'AccessControl');
 * 	public function index() {
 * 		if ($this->AccessControl->validUser()) {
 * 			Log::write(Log::ERROR, 'invalid user detected');
 * 		}
 * 	}
 * }
 * </code>
 * <br />
 * 
 * You also can use the shortcut function, defined in core.php:
 * <code>
 * logg(Log::ERROR, 'error found!');
 * </code>
 * <br />
 * 
 * It’s also possible to create your own log files by passing a string as first
 * parameter:
 * <code>
 * logg('app', 'user logged in'.$User->name);
 * </code>
 * This will log a message in <code>/app/tmp/log/app.log</code>
 * 
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 27.12.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @version 0.1
 * @uses File
 * @uses String
 * @uses Charset
 * @uses ArrayHelper
 */
class Log extends AppComponent {
	
	/**
	 * Base path were log files are stored
	 * @var string
	 */
	public static $path = LOG_DIR;
	
	/**
	 * Standarized english date format, that should be standard for all log
	 * files.
	 * @var string
	 */
	public static $dateFormat = 'Y-m-d H:i:s';
	
	/**
	 * Log Message format
	 * // todo extract this to optional new class ?
	 * @var string
	 */
	public static $format = ':date [:ip] :message';
	
	/**
	 * Extension of log files
	 * @var string
	 */
	public static $extension = '.log';
	
	private static $instance;
	
	public static $level = self::ERROR;
	
	/**
	 * @todo explain the different mechanics behind the levels
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
	 * Log Severity levels => filename
	 * @var array(string)
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

	/**
	 * Returns an instance of Log
	 * @return Log
	 */
	public static function getInstance() {
  		if (!self::$instance) {
  			$classname = __CLASS__;
  			self::$instance = new $classname();
  		}
  		return self::$instance;
  	}

  	/**
  	 * Write a message to the log.
  	 * 
  	 * The Filename of the log depends on the $level you pass. If the file
  	 * doesn't exist it will be created.
  	 * 
  	 * Arrays will be flattened and objects are tried to convert to a string
  	 * by using __toString or render().
  	 * All Log Messages are stripped from new line characters
  	 * <code>
  	 * // in the controller, add a message to the log
  	 * $this->Log(Log::INFO, 'So we tested this baby!');
  	 * </code>
  	 * @param integer $level
  	 * @message message that should be logged
  	 * @return boolean true on success
  	 */
	public static function write($level, $message) {
		// log message only if level is higher or equal current reporting level
		if ($level <= self::$level) {
			$logFile = new File(self::logFileName($level));
			if (!$logFile->exists()) {
				$logFile->create();
			}
			if ($logFile->writable()) {
				$logFile->append(self::getInstance()->createLogMessage($message));
			}
		}
		return true;
	}
	
	/**
	 * Formats the log message
	 *
	 * @param mixed $message
	 * @return string
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
	 * Stores the translated log file names as cache
	 * @var array(string)
	 */
	public static $filenameCache = array();
	
	/**
	 * Translates the log level to a full path to the log filename and returns it
	 * This method takes care of invalid characters, spaces and empty levels.
	 * The filename is generally speaking created using the {@levels} array as 
	 * soon the level is known - otherwise a filename is created depending
	 * on the level name.
	 * 
	 * @param integer $level
	 * @return string
	 */
	public static function logFileName($level) {
		if (!isset(self::$filenameCache[$level])) {
			if (in_array($level, array(self::ERROR, self::WARNING))) {
				$filename = 'error';
			} elseif (array_key_exists($level, self::$levels)) {
				$filename = self::$levels[$level];
			} elseif (!empty($level)) {
				$filename = $level;
			} else {
				$filename = 'unknown_level';
			}
			// cleanup the filename
			$filename = Sanitizer::filename($filename);
			if (empty($filename)) {
				$filename = 'unknown_level';
			}
			// return the resulting path to the logfile
			self::$filenameCache[$level] = realpath(self::$path).DS.$filename.self::$extension;
		} 
		return self::$filenameCache[$level];
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class LogException extends ComponentException {}