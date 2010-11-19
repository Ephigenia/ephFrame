<?php

namespace ephFrame\view;

class View
{
	protected $renderer;
	
	public $data = array();
	
	public $type = 'html';
	
	public $theme = 'default';
	
	public function __construct()
	{
		$this->renderer = new \ephFrame\view\Renderer();
	}
	
	public function render($path)
	{
		// path = "{APP_ROOT}/view/$theme/$controller/$action.$type.$extension";
		$filename = APP_ROOT.'/view/'.$this->theme.'/'.$path.'.'.$this->type.'.php';
		return $this->renderer->render($filename, $this->data);
	}
}