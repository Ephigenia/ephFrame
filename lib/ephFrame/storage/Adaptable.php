<?php

namespace ephFrame\storage;

class Adaptable extends \ArrayObject
{
	protected $adapter;
	
	public static $options = array();
	
	public static function config(Array $options = array())
	{
		if (func_num_args() == 0) {
			return self::$options;
		}
		self::$options += $options;
	}
	
	public function offsetGet($key)
	{
		return $this->adapter()->get($key);
	}
	
	public function offsetSet($key, $value)
	{
		return $this->adapter()->set($key, $value);
	}
	
	public function offsetExists($key)
	{
		return $this->adapter()->offsetExists($key);
	}
	
	public function offsetUnset($key)
	{
		return $this->adapter()->offsetUnset($key);
	}
	
	protected function adapter()
	{
		if (!isset($this->adapter)) {
			$class = get_class($this);
			$this->adapter = new $class::$options['adapter'](self::$options);
			$this->adapter->start();
		}
		return $this->adapter;
	}
	
	public function clear()
	{
		return $this->adapter()->clear();
	}
}