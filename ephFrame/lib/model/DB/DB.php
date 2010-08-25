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

// load parent class
Library::load('ephFrame.lib.model.DB.QueryHistory');
Library::load('ephFrame.lib.model.DB.DBDSN');
Library::load('ephFrame.lib.model.DB.DBInterface');

/**
 * Abstract Database-Access-Object (DAO)
 * 
 * This abstract class is the base class for every other DAO used in the
 * framework.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 19.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.model.DB
 * @version 0.2
 */
abstract class DB extends Object implements DBInterface
{	
	/**
	 * Stores the current {@link DBDSN} for this DAO
	 * @var DBDSN
	 */
	public $DBDSN;
	
	/**
	 * Stores the ressource index for the opened connection
	 * @var ressource
	 */
	protected $connectionHandle;
	
	/**
	 * Stores all queries send to the dao including the result id and a
	 * {@link Timer}.
	 * @var DAOQueryHistory
	 */
	public $queries;
	
	/**
	 * Create the DAO,
	 * create the {@link DAOQueryHistory} which stores all the queries and
	 * their performance.
	 * @return DAO
	 */
	final public function __construct() 
	{
		$this->DBDSN = new DBDSN('');
		$this->queries = new QueryHistory();
		return $this;
	}
	
	/**
	 * Tests if the DAO has established a connection
	 * @return boolean
	 */
	public function connected() 
	{
		return is_resource($this->connectionHandle);
	}
	
	/**
	 * Tests if a connection is established and will throw {@link DBNotConnectedException}
	 * if not.
	 * @throws DBNotConnectedException
	 */
	public function checkConnection() 
	{
		if (!$this->connected()) {
			throw new DBNotConnectedException();
		}
		return $this;
	}
	
	/**
	 * Conect to the database server using the passed {@link DBDSN}
	 * @param DBDSN $dbdsn
	 * @return DAO
	 */
	public function connect(DBDSN $dbdsn) 
	{
		$this->DBDSN = $dbdsn;
		if (!$this->beforeConnect()) return false;
		return $this;
	}
	
	/**
	 * Before connect callback, overwrite this in the dao subclass
	 * the subclass should not establish a connection if {@link beforeConnect}
	 * returns false.
	 * @return boolean
	 */
	public function beforeConnect() 
	{
		return true;	
	}
	
	/**
	 * This is called after the connection is established. Overwrite this
	 * in your subclasses.
	 * @return DAO
	 */
	public function afterConnect() 
	{
		if ($this->DBDSN->db()) {
			$this->selectDB($this->DBDSN->db());
		}
		if ($this->DBDSN->charset()) {
			$this->selectCharset($this->DBDSN->charset());
		}
		return $this;
	}
	
	/**
	 * Sets or returns the DBDSN which is used to establish the connection
	 * @param DBDSN $dbdsn
	 * @return DBDSN
	 */
	public function DBDSN(DBDSN $dbdsn = null) 
	{
		return $this->__getOrSet('DBDSN', $dbdsn);
	}
	
	/**
	 * Callback called before Query is send to the database
	 * @param string
	 */
	public function beforeQuery($query) 
	{
		$this->checkConnection();
		if (!empty($query)) return $query;
		return false;
	}
	
	/**
	 * Sends a Query to the Database and return a result (what a surprise!)
	 * @param string|DBQuery $query
	 * @return QueryResult
	 */
	public function query($query) 
	{ }
	
	/**
	 * Locks a table
	 *
	 * @param string $tablename
	 * @return boolean
	 */
	public function lockTable($tablename) 
	{
		$this->query('LOCK TABLE '.$tablename.';');
		return true;
	}
	
	/**
	 * Unlocks a locked table
	 *
	 * @param string $tablename
	 * @return boolean
	 */
	public function unlockTable($tablename) 
	{
		$this->query('UNLOCK TABLE '.$tablename.';');
		return true;
	}
	
	/**
	 * Returns the last id of the item that was inserted
	 * @return integer
	 */
	public function lastInsertId() 
	{}
	
	/**
	 * Select a specific database
	 * @param string $dbname
	 * @return boolean
	 */
	public function selectDB($dbname = null) 
	{ }
	
	/**
	 * Returns the code for the last error that occured
	 * @return integer|boolean
	 */
	public function errorNo() 
	{ }
	
	/**
	 * Returns the message of the last error occured
	 * @return string
	 */
	public function errorMessage() 
	{ }
	
	public function disconnect() 
	{ }
	
	/**
	 * Returns a {@link Hash} with all table columns and information about them
	 * @return Hash
	 */
	public function describe($tablename) 
	{
		if (!class_exists('DBDescribeQuery')) {
			Library::load('ephFrame.lib.model.DB.DBDescribeQuery');
		}
		$query = new DBDescribeQuery($tablename);
		$result = $this->query($query);
		return $result->fetchAll('assoc');
	}
	
	/**
	 * Returns the last query performed
	 * @return DBQuery
	 */
	final public function lastQuery() 
	{
		return $this->queries->last();
	}
	
	public function __destroy() 
	{
		if ($this->connected()) {
			$this->disconnect();
		}
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DBException extends BasicException 
{ }

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DBNotConnectedException extends DBException 
{}