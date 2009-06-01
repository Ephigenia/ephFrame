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
 * 	Basic Exception Class
 * 	Every Exception in a ephFrame Project should be extended by this
 * 	for keeping track of exception family tree
 * 
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class BasicException extends Exception {
	
	const FATAL = 1;
	const ERROR = 2;
	const NOTICE = 8;
	const INTRUSION = 16;
	
	public $created;
	public $level;
	
	public function __construct($message = null) {
		if ($message !== null) $this->message = $message;
		$this->created = time();
		if (!isset($this->level)) $this->level = self::ERROR;
		// @todo finish all data in the silent verbose log
		if (class_exists('Log') && is_writable(LOG_DIR) && is_writable(Log::logFileName(Log::VERBOSE_SILENT))) {
			$logMessage = 'ephFrame: Exception thrown \''.get_class($this).'\'';
			if (!empty($message)) {
				$logMessage .= ', message: \''.$message.'\'';
			} else {
				$logMessage .= ', no message';
			}
			logg(Log::VERBOSE_SILENT, $logMessage);
		}
	}
	
}

/**
 * 	Thrown in __clone functions that do not allow cloning of a class
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class NotClonableException extends BasicException {
	public function __construct(Object $object) {
		$this->message = 'This object of class \''.get_class($object).'\' can not be cloned.';
		parent::__construct();
	}
}

?>