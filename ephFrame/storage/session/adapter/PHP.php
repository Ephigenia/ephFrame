<?php

namespace ephFrame\storage\session\adapter;

class PHP extends \ArrayObject
{
	protected $options = array(
		'name' => 'SESSION',
		'httponly' => false,
	);
	
	public function __construct(Array $options = array())
	{
		$this->options += $options + session_get_cookie_params();
		return parent::__construct(array(), \ArrayObject::ARRAY_AS_PROPS);
	}
	
	public function start()
	{
		extract($this->options);
		session_name($name);
		session_cache_limiter(false);
		session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
		if (isset($id) && $id != $this->id()) {
            session_id($id);
        }
        session_start();
		$this->exchangeArray($_SESSION);
		return $this;
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
		return parent::offsetSet($key, $value);
	}
	
	public function clear()
	{
		$this->exchangeArray(array());
		return $this;
	}
	
	public function __destroy() 
	{
		session_write_close();
		parent::__destroy();
		return true;
	}
}