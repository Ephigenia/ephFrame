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

// load parent class
ephFrame::loadClass('ephFrame.lib.model.DB.DB');
ephFrame::loadClass('ephFrame.lib.model.DB.MySQL.MySQLException');
ephFrame::loadClass('ephFrame.lib.model.DB.MySQL.MySQLQueryResult');
ephFrame::loadClass('ephFrame.lib.helper.Timer');
ephFrame::loadClass('ephFrame.lib.model.DB.DBQuery');

/**
 * 	MySQL Database-Access-Object (DAO)
 * 
 * 	The MySQL DAO is made for accessing data from a MySQL Server. It should
 * 	provide all methods the {@link DAO} provide.
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 *	@since 19.05.2007
 *	@package ephFrame
 *	@subpackage ephFrame.lib.model.DB.MySQL
 * 	@version 0.2
 */
class MySQL extends DB implements DBInterface {
	
	/**
	 *	Connect to a MySQL Server
	 * 
	 * 	This will try to set up a non-persistent connection to a MySQL Server
	 * 	using the {@link DBDSN} passed to the method. If any error occurs a
	 * 	{@link MySQLConnectionErrorException} is thrown that stores information
	 * 	about the error that occured.
	 * 	After a successfull connect {@link afterConnect} is called.
	 * 	
	 * 	@param DBDSN $dbdsn
	 * 	@throws MySQLHandleConnectionException
	 * 	@return DAOMySQL
	 */
	public function connect(DBDSN $dbdsn) {
		$this->DBDSN($dbdsn);
		if (!$this->beforeConnect()) return false;
		$this->connectionHandle = @mysql_connect($this->DBDSN->host(), $this->DBDSN->user(), $this->DBDSN->pass());
		// throw an expetion if the connaction failed
		if (!$this->connectionHandle) {
			logg(Log::VERBOSE_SILENT, 'ephFrame: MySQL Connecting to \''.$this->DBDSN->host().'\' failed');
			MySQLException::evoke($this);
		}
		logg(Log::VERBOSE_SILENT, 'ephFrame: MySQL Connected to \''.$this->DBDSN->host().'\'');
		$this->afterConnect();
		return $this;
	}
	
	/**
	 *	Selects a specific DB. If no $dbname is passed the DB Name from the
	 * 	{@link DBDSN} which was used to connect to the database is used.
	 * 	@param string $dbName
	 * 	@throws MySQLException
	 * 	@return boolean
	 */
	public function selectDB($dbName = null) {
		if ($dbName === null) $dbName = $this->DBDSN->db();
		if (!is_string($dbName)) throw new StringExpectedException();
		$this->checkConnection();
		if (!mysql_select_db($dbName, $this->connectionHandle)) {
			logg(Log::VERBOSE_SILENT, 'ephFrame: MySQL failed to use DB \''.$this->DBDSN->db().'\'');
			MySQLException::evoke($this);
		}
		logg(Log::VERBOSE_SILENT, 'ephFrame: MySQL uses DB \''.$this->DBDSN->db().'\'');
		return true;
	}
	
	/**
	 *	Selects a specific charset
	 * 	@param string $charset
	 * 	@return bolean
	 */
	public function selectCharset($charset) {
		return $this->query('SET NAMES '.DBQuery::quote($charset));
	}
	
	/**
	 *	BeforeQuery Callback, called before a query is send
	 * 	@param string|DBSelectQuery
	 * 	@return string|DBSelectQuery
	 */
	public function beforeQuery($query) {
		return parent::beforeQuery($query);
	}
	
	/**
	 *	Sends a Query (@link DBQuery} to the Database and returns 
	 * 	a {@link DBResult} object with the results
	 * 	@param DBQuery $query
	 * 	@return QueryResult
	 * 	@throws MySQLException
	 */
	public function query($query) {
		if (!($query = $this->beforeQuery($query))) {
			return false;	
		}
		if (is_object($query)) {
			if (method_exists($query, '__toString')) {
				$renderedQuery = $query->__toString();
			} elseif ($query instanceof Renderable && method_exists($query, 'render')) {
				$renderedQuery = $query->render();
			} else {
				trigger_error('Query object is not renderable.', E_USER_ERROR);
			}
		} else {
			$renderedQuery = $query;
		}
		// finally perform the query
		$queryTimer = new Timer();
		$result = @mysql_query($renderedQuery, $this->connectionHandle);
		$queryTimer->stopTimer();
		// check for errors and throw exception
		if (!$result) {
			$this->queries->add($query, new MySQLQueryResult($result), $queryTimer);
			MySQLException::evoke($this);
		} else {
			$queryResult = new MySQLQueryResult($result);
			$this->queries->add($query, $queryResult, $queryTimer);
		}
		return $queryResult;
	}
	
	/**
	 * 	Returns the last id of the item that was inserted
	 *	@return integer
	 */
	public function lastInsertId() {
		$this->checkConnection();
		return mysql_insert_id($this->connectionHandle);
	}
	
	/**
	 * 	Returns the error number of the last error that occured, except no error
	 * 	has occured.
	 * 	@return integer|boolean
	 */
	public function errorNo() {
		return mysql_errno();
	}
	
	/**
	 *	Returns the error message for the last error that occured, except no
	 * 	error has occured
	 * 	@return string|boolean
	 */
	public function errorMessage() {
		return mysql_error();
	}
	
}

?>