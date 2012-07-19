<?php

namespace ephFrame\core;

class CallbackHandler extends \ArrayObject
{
	public function __construct(Array $array = array())
	{
		return parent::__construct($array, \ArrayObject::ARRAY_AS_PROPS);
	}
	
	public function add($name, Array $callback)
	{
		$this[(string) $name][] = $callback;
		return $this;
	}
	
	public function call($name, Array $arguments = array())
	{
		if (!isset($this[$name])) return true;
		$result = true;
		foreach($this[$name] as $callback) {
			if ($callback[0] instanceof \Closure) {
				$result = call_user_func_array($callback[0], $callback[1]);
			} elseif (method_exists($callback[0], $callback[1])) {
				$result = call_user_func_array(array($callback[0], $callback[1]), $arguments);
			}
		}
		return $result;
	}
}