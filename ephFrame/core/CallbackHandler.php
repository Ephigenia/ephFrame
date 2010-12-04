<?php

namespace ephFrame\core;

class CallbackHandler
{
	protected $callbacks = array();
	
	public function add($name, $callback)
	{
		$this->observers[(string) $name][] = $callback;
	}
	
	public function call($name, Array $args = array())
	{
		if (!isset($this->callbacks[$name])) return true;
		foreach($this->callbacks[$name] as $callback) {
			$results += call_user_func_array($callback);
		}
		return $results;
	}
	
	public function __invoke($name, Array $args = array()) {
		return $this->callback($name, $args);
	}
}