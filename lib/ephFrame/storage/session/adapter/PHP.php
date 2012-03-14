<?php

namespace ephFrame\storage\session\adapter;

class PHP extends \ArrayObject
{
	public static $options = array(
		'name' => 'SESSID',
		'httponly' => false,
		'lifetime' => 86400, // 24 hours
		'domain' => null,
		'secure' => false,
	);
	
	public function __construct(Array $data = array(), Array $options = array())
	{
		self::$options += $options + session_get_cookie_params();
		return parent::__construct($data, \ArrayObject::ARRAY_AS_PROPS);
	}
	
	public function start()
	{
		$result = true;
		if (!isset($_SESSION)) {
			extract(self::$options);
			session_name($name);
			session_cache_limiter(false);
			session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
			if (isset($id) && $id != $this->id()) {
	            session_id($id);
	        }
			$result = session_start();
		}
		$this->exchangeArray($_SESSION);
		return $result;
	}
	
	public function id()
	{
		return session_id();
	}
	
	public function get($key)
	{
		return parent::offsetGet($key);
	}
	
	public function set($key, $value)
	{
		if (is_array($value)) {
			$value = new self($value);
		}
		if ($key == null) {
			$_SESSION[] = $value;
		} else {
			$_SESSION[$key] = $value;
		}
		return parent::offsetSet($key, $value);
	}
	
	public function offsetUnset($key)
	{
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
			parent::offsetUnset($key);
		}
		return true;
	}
	
	public function clear()
	{
		$this->exchangeArray($_SESSION = array());
		return $this;
	}
	
	public function toArray() { 
		$data = $this->data; 
		foreach ($data as $key => $value) {
			if ($value instanceof self) {
				$data[$key] = $value->toArray();
			}
		}
		return $data; 
	}
	
    public function __clone()
	{ 
        foreach ($this->data as $key => $value) if ($value instanceof self) $this[$key] = clone $value; 
    }
	
	public function __destroy() 
	{
		session_write_close();
		parent::__destroy();
		return true;
	}
}