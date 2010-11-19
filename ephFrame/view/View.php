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
		$filename = APP_ROOT.'/view/default/app/index.php';
		return $this->renderer->render($filename, $this->data);
	}
}