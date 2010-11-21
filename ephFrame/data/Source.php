<?php

namespace ephFrame\data;

class Source
{
	protected $connection;
	
	abstract public function connect();
	
	abstract public function disconnect();
	
	abstract public function describe();
	
	public function isConnected()
	{
		return $this->connection;
	}
	
	public function __destruct()
	{
		if ($this->connection) {
			$this->disconnect();
		}
	}
}