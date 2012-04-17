<?php

namespace ephFrame\HTTP;

class Cookie
{
	public $name;
	
	public $value;
	
	public $domain;
	
	public $expire;
	
	public $path;
	
	public $secure;
	
	public $httponly = true;
	
	public function __construct(
		$name,
		$value = null,
		$domain = null,
		$expire = null,
		$path = null,
		$secure = null,
		$httponly = null
	) {
		if (is_array($name)) {
			extract($name);
		}
		$this->name = $name;
		if ($value != null) {
			$this->value = $value;
		} elseif (isset($_COOKIE[$this->name])) {
			$this->value = $_COOKIE[$this->name];
		}
		$this->domain = $domain ?: null;
		if ($expire !== null) {
			if (is_string($expire)) {
				$this->expire = strtotime($expire);
			} else {
				$this->expire = (int) $expire;
			}
		}
		if ($path !== null) {
			$this->path = $path;
		}
		if ($secure !== null) {
			$this->secure = (bool) $secure;
		}
		if ($httponly !== null) {
			$this->httponly = (bool) $httponly;
		}
		$this->store();
	}
	
	public function store()
	{
		setcookie(
			$this->name,
			$this->value,
			$this->expire,
			$this->path,
			$this->domain,
			$this->secure,
			$this->httponly
		);
	}
	
	public function delete()
	{
		unset($_COOKIE[$this->name]);
		return setcookie($this->name, NULL, -1);
	}
}