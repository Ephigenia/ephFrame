<?php

namespace ephFrame\logger\adapter;

use
	\ephFrame\logger\Event,
	\ephFrame\logger\Logger
	;

class FirePHP extends Adapter
{
	protected $firephp;
		
	protected $priorityToStyle = array(
		Logger::EMERGENCY => \FirePHP::ERROR,
		Logger::ALERT => \FirePHP::ERROR,
		Logger::CRITICAL => \FirePHP::ERROR,
		Logger::ERROR => \FirePHP::ERROR,
		Logger::WARNING => \FirePHP::WARN,
		Logger::NOTICE => \FirePHP::INFO,
		Logger::INFO => \FirePHP::INFO,
		Logger::DEBUG => \FirePHP::LOG,
	);
	
	public function __construct(\FirePHP $instance)
	{
		$this->firephp = $instance;
		return parent::__construct();
	}
	
	public function write(Event $event)
	{
		$this->firephp->{strtolower($this->priorityToStyle[$event->priority])}($this->formater->format($event), $this->priorityToStyle[$event->priority]);
	}
}