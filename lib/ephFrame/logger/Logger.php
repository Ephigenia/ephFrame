<?php

namespace ephFrame\logger;

class Logger
{
	const EMERGENCY = 0;
	const ALERT = 1;
	const CRITICAL = 2;
	const ERROR = 3;
	const WARNING = 4;
	const NOTICE = 5;
	const INFO = 6;
	const DEBUG = 7;
	
	protected $adapters = array();
	
	protected $filters = array();
	
	protected $priorities = array();
	
	public function __construct($adapter)
	{
		$r = new \ReflectionClass($this);
        $this->priorities = array_map('strtolower', array_flip($r->getConstants()));
		$this->adapters[] = $adapter;
	}
	
	public function write($priority, $message, Array $options = array())
	{
		$event = new Event($priority, strtolower($this->priorities[$priority]), $message);
		foreach($this->filters as $filter) {
			if (!$filter->accept($event)) {
				return false;
			}
		}
		foreach($this->adapters as $Adapter) {
			$Adapter->write($event);
		}
		return true;
	}
	
	public function __call($priority, Array $params = array())
	{
		$params += array(null, array());
		return $this->write(array_search($priority, $this->priorities), $params[0], $params[1]);
	}
}