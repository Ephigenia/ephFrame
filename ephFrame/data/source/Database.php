<?php

namespace ephFrame\data\source;

use PDO;

abstract class Database extends \ephFrame\data\Source
{
	protected $connection;
	
	public $logger = array();
	
	public $lastQuery;
	
	public function connect($dsn, $username = '', $password = '', Array $options = array())
	{
		$options += array(
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8;',
		);
		$this->connection = new PDO($dsn, $username, $password, $options);
	}
	
	public function disconnect()
	{
		if ($this->isConnected()) {
			unset($this->connection);
		}
		return $this;
	}
	
	public function isConnected()
	{
		return !empty($this->connection);
	}
	
	public function query($query)
	{
		$this->lastQuery = $query;
		foreach($this->logger as $logger) $logger->log($query);
		$statement = $this->connection->prepare($query);
		$statement->execute();
		return $statement;
	}
	
	public function __destruct()
	{
		$this->disconnect();
	}
}

class DatabaseException {}
class DatabaseConnectionException extends DatabaseException {}