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
 * 	Exception for MySqlQuery
 * 
 * 	MySQL Exception Factory Class, call {@link evoke} for throwing a MySQL
 * 	Exception depending on the current MySQL Error.
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 06.07.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.model.DB.MySQL
 */
class MySQLException extends DBException {
	
	const ERRNO_DEFAULT = 0;
	const ERRNO_ACCESS_DENIED = 1045;
	const ERRNO_NO_DB_SELECTED = 1046;
	const ERRNO_DB_NOT_FOUND = 1049;
	const ERRNO_SERVER_SHUTDOWN_IN_PROGRESS = 1053;
	const ERRNO_QUERY_ERROR = 1064;
	const ERRNO_TABLE_NOT_ACCESSIBLE = 1105;
	const ERRNO_TABLE_NOT_FOUND = 1146;
	const ERRNO_DUBLICATE_ENTRY = 1062;
	const ERRNO_TABLE_CRASHED = 1194;
	const ERRNO_CONNECTION_ERROR = 2003;
	const ERRNO_SERVER_GONE_AWAY = 2006;
	const ERRNO_LOST_CONNECTION_QUERY = 2013;
	
	/**
	 *	Stores the Current MySQL Error Number
	 * 	List of Error Codes:
	 * 	{@link http://www.mysql.org/doc/refman/5.0/en/error-messages-server.html}
	 * 	@var integer
	 */
	public $mysqlErrorNo;
	
	/**
	 *	Stores the current MySQL Error String
	 * 	@var string
	 */
	public $mysqlErrorMessage;
	
	public function __construct(MySQL $dao) {
		$this->mysqlErrorMessage = $dao->errorMessage();
		$this->mysqlErrorNo = $dao->errorNo();
		// put some information in any mysql exception that might have no message
		if (empty($this->message)) {
			$this->message = 'MySQL Error ('.$this->mysqlErrorNo.'): '.$this->mysqlErrorMessage;
		}
		// add last query to exception message
		if (count($dao->queries) > 0) {
			$this->message .= LF.LF.'Last Query: '.LF.$dao->queries->last().LF;
		}
		parent::__construct($this->message);
	}
	
	public static function evoke(MySQL $MySQL) {
		if ($MySQL->errorNo()) {
			$exceptionName = self::exceptionName();
			if ($exceptionName) throw new $exceptionName($MySQL);
		}
		return false;
	}
	
	/**
	 *	Determines the exception name that fits to the
	 * 	current mysql error
	 * 	@return string
	 */
	public static function exceptionName() {
		$errorNo = mysql_errno();
		switch ((int) $errorNo) {
			case self::ERRNO_ACCESS_DENIED:
				return 'MySQLConnectionAccessDeniedException';
				break;
			case self::ERRNO_CONNECTION_ERROR:
				return 'MySQLConnectionException';
				break;
			case self::ERRNO_LOST_CONNECTION_QUERY:
				return 'MySQLQueryLostConnectionException';
				break;
			case self::ERRNO_SERVER_GONE_AWAY:
				return 'MySQLServerGoneAwayException';
				break;
			case self::ERRNO_SERVER_SHUTDOWN_IN_PROGRESS:
				return 'MySQLServerShutdownInProgressException';
				break;
			case self::ERRNO_TABLE_CRASHED:
				return 'MySQLTableCrashedException';
				break;
			case self::ERRNO_TABLE_NOT_ACCESSIBLE:
				return 'MySQLTableNotAccessibleException';
				break;
			case self::ERRNO_TABLE_NOT_FOUND:
				return 'MySQLTableNotFoundException';
				break;
			case self::ERRNO_DUBLICATE_ENTRY:
				return 'MySQLQueryDuplicateEntryException';
				break;
			case self::ERRNO_NO_DB_SELECTED:
				return 'MySQLNoDBSelectedException';
				break;
			case self::ERRNO_DB_NOT_FOUND:
				return 'MySQLDBNotFoundException';
				break;
			case self::ERRNO_QUERY_ERROR:
			case self::ERRNO_DEFAULT:
			default:
				return 'MySQLQueryException';
				break;
		}
		return false;
	}
}


/**
 *	Thrown if php has no mysql module
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception 
 */
class MySQLNotInstalledException extends MySQLException {
	public function __construct(MySQL $dao, $query) {
		$this->message = 'MySQL seems not to be activated or compiled in this php installation.';
		parent::__construct($dao);
	}
}

/**
 *	Thrown if a query returns with a errous result
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib 
 */
class MySQLQueryException extends MySQLException {
	public function __construct(MySQL $dao, $query = null) {
		$this->level = self::FATAL;
		parent::__construct($dao);
	}
}

/**
 *	Thrown if a query is empty
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib 
 */
class MySQLQueryEmptyException extends MySQLQueryException {
	public function __construct(MySQL $dao) {
		$this->message = 'MySQL seems not to be activated or compiled in this php installation.';
		parent::__construct('The query that should be performed is empty.');
	}
}

/**
 * 	Thrown when a mysql connection could not be established
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLConnectionErrorException extends MySQLException {
	public function __construct(MySQL $dao) {
		$this->level = self::FATAL;
		parent::__construct($dao);
	}
}

/**
 *	Thrown if a empty database name was detected
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLDBNameEmptyException extends MySQLException {
	public function __construct(MySQL $dao) {
		$this->message = 'Database name was empty. Unable to select database with no name.';
		parent::__construct($dao);
	}
}

/**
 *	Thrown the connection to MySQL Server was lost during a query
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLQueryLostConnectionException extends MySQLQueryException{}

/**
 *	Thrown if a MySQL Error occured during a query
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLQueryDuplicateEntryException extends MySQLQueryException{}


/**
 *	Thrown upon a MySQL Connection Error
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLConnectionException extends MySQLException {}

/**
 *	Thrown if PHP was unable to connect with the given user/password combination 
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLConnectionAccessDeniedException extends MySQLConnectionException {}

/**
 * 	Thrown if the MySQL Server gone away (probably crashed)
 *	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLServerGoneAwayException extends MySQLConnectionException {}

/**
 *	Thrown possibly if MySQL Server went away (probably crashed)
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLServerShutdownInProgressException extends MySQLConnectionException {}


/**
 * 	Thrown if a MySQL Error occured that has something to do with a database
 *	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLDBException extends MySQLException {}

/**
 *	Thrown if no table was selected somewhere
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLNoDBSelectedException extends MySQLDBException {
	public function __construct(MySQL $dao) {
		$this->message = 'The current dao has no database selected. Please select a database.';
		parent::__construct($dao);
	}
}

/**
 * 	Thrown if a database was not found
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLDBNotFoundException extends MySQLDBException {
	public function __construct(MySQL $dao) {
		$this->message = 'Database Table named '.$dao->DBDSN->db().' not found.';
		parent::__construct($dao);
	}
}

/**
 * 	Thrown if a MySQL Error occured that has something to do with a database table
 *	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLTableException extends MySQLException {}

/**
 *	Thrown if a table was found that is marked as chrashed
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLTableCrashedException extends MySQLTableException {
	public function __construct(MySQL $dao) {
		$tablename = preg_match_first($dao->errorMessage(), "!^Table \'([\w\d\.]*)\'!");
		$this->message = 'Database Table named \''.$tablename.'\' is marked as crashed.';
		parent::__construct($dao);
	}
}

/**
 *	Thrown if a table was not accessible
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLTableNotAccessibleException extends MySQLTableException {
	public function __construct(MySQL $dao) {
		$tablename = preg_match_first($dao->errorMessage(), "!^Table \'([\w\d\.]*)\'!");
		$this->message = 'Database Table \''.$tablename.'\' is not accessible.';
		parent::__construct($dao);
	}
}

/**
 * 	Thrown if a table was not found
 * 	// FINISH
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class MySQLTableNotFoundException extends MySQLTableException {
	/**
	 * 	@var string
	 */
	public $tablename = '';
	public function __construct(MySQL $dao) {
		$this->tablename = preg_match_first($dao->errorMessage(), "!^Table \'([\w\d\.]*)\'!");
		$this->message = 'Database Table \''.$this->tablename.'\' not found.';
		parent::__construct($dao);
	}
}
 
?>