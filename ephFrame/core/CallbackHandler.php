<?php

namespace ephFrame\core;

class CallbackHandler
{
	protected $callbacks = array();
	
	public function add($name, Array $callback)
	{
		$this->callbacks[(string) $name][] = $callback;
		return $this;
	}
	
	public function call($name, Array $arguments = array())
	{
		if (!isset($this->callbacks[$name])) return true;
		$result = true;
		foreach($this->callbacks[$name] as $callback) {
			if (!method_exists($callback[0], $callback[1])) continue;
			$result = call_user_func_array(array($callback[0], $callback[1]), $arguments);
			if (!$result) {
				return $result;
			}
		}
		return $result;
	}
}