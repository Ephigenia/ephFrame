<?php

namespace ephFrame\view;

class Renderer
{
	protected $data = array();
	
	protected $template = 'index';
	
	public function __construct($template, Array $data = array())
	{
		$this->data = $data;
		$this->template = $template;
	}
	
	public function render()
	{
		ob_start();
		require APP_ROOT.'/view/'.$this->template.'.php';
		return ob_get_clean();
	}
}