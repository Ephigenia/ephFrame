<?php

namespace ephFrame\data;

class Connections
{
	private static $connections = array();
	
	public static function add($name, $config = array())
	{
		$config += array(
			'dsn' => false,
			'user' => false,
			'password' => false,
			'adapter' => false,	
		);
		return static::$connections[$name] = $config;
	}
	
	public static function get($name)
	{
		if (!isset(static::$connections[$name])) {
			throw new ConnectionsConnectionNotFoundException($name);
		}
		if (!isset(static::$connections[$name]['object'])) {
			extract(static::$connections[$name]);
			$connection = new $adapter();
			$connection->connect($dsn, $user, $password);
			static::$connections[$name]['object'] = $connection;
		}
		return static::$connections[$name]['object'];
	}
}

class ConnectionsException extends \Exception {}
class ConnectionsConnectionNotFoundException extends ConnectionsException
{
	public function __construct($name) {
		return parent::__construct(sprintf('No connection "%s" found', $name));
	}
}