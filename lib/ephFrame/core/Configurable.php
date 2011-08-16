<?php

namespace ephFrame\core;

abstract class Configurable
{
	public function __construct(Array $options = array())
	{
		$this->fromArray($options);
	}
	
	public function fromArray(Array $options = array())
	{
		foreach($options as $k => $v) {
			if (is_array($this->{$k}) && is_array($v)) {
				$this->{$k} = $v + $this->{$k};
			} else {
				$this->{$k} = $v;
			}
		}
		return $this;
	}
}