<?php

namespace ephFrame\logger\adapter;

use 
	\ephFrame\logger\Event
	;

class File extends Adapter
{
	protected $fp;
	
	protected $filename;
	
	public function __construct($filename)
	{
		$this->filename = (string) $filename;
		return parent::__construct();
	}
	
	public function write(Event $event)
	{
		if (!$this->fp) {
			$this->fp = fopen($this->filename, 'w+');
		}
		fputs($this->fp, $this->formater->format($event));
		return true;
	}
	
	public function __destruct()
	{
		if ($this->fp) {
			fclose($this->fp);
		}
	}
}