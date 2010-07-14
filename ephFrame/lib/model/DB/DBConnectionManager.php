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

/**
 * DB Connection Manager Class
 * 
 * The DB Connection Manager establishes and stores database connections and
 * their handles.
 * 
 * This is used in all {@link Models}.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 04.09.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.model.DB
 */
class DBConnectionManager extends Object 
{	
	/**
	 * @var DBConnectionManager
	 */
	public static $instance;
	
	/**
	 * @var DBFactory
	 */
	protected $factory;
	
	/**
	 * Staticly stores all established connections
	 * @var array(DB)
	 */
	public $connections = array();
	
	public function __construct() 
	{
		ephFrame::loadClass('ephFrame.lib.model.DB.DBFactory');
		ephFrame::loadClass('ephFrame.lib.model.DB.DBDSN');
		$this->factory = new DBFactory();
	}
	
	/**
	 * @return DBConnectionManager
	 */
	public static function getInstance() {
		if (empty(self::$instance)) {
			self::$instance = new DBConnectionManager();
		}
		return self::$instance;
	}
	
	/**
	 * Tries to establish a database connection using the $DBConfigName from
	 * {@link DBConfig} class if this connection was not established before.
	 * 
	 * @param string $DBConfigName
	 */
	public function get($DBConfigName) 
	{
		$instance = self::getInstance();
		// open conenction and place connection in {@link data} when successfully connected
		if (!$instance->opened($DBConfigName)) {
			$DBConfig = new DBConfig();
			$DBDSN = new DBDSN($DBConfig->$DBConfigName);
			$DB = $this->factory->create($DBDSN->type());
			$DB->connect($DBDSN);
			$this->connections[$DBConfigName] = $DB;
		}
		return $instance->connections[$DBConfigName];
	}
	
	public function opened($DBConfigName) 
	{
		return array_key_exists($DBConfigName, self::getInstance()->connections);
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DBConnectionManagerException extends BasicException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DBConnectionManagerDBConfigNotFoundException extends DBConnectionManagerException 
{
	public function __construct($DBConfigName) 
	{
		$this->message = 'Unable to find \''.$DBConfigName.'\' in DBConfig.';
		parent::__construct($this->message);
	}
}