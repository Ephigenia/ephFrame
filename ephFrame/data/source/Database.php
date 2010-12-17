<?php

namespace ephFrame\data\source;

class Database extends \ephFrame\data\Source
{
	protected $connection;
	
	public function connect()
	{
		
	}
	
	public function disconnect()
	{
		
	}
	
	public function describe()
	{
		
	}
	
	public function isConnected()
	{
		return $this->connection;
	}
	
	public function __destruct()
	{
		if ($this->isConnected()) {
			$this->disconnect();
		}
	}
}