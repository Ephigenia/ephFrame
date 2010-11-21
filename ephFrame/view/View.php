<?php

namespace ephFrame\view;

class View
{
	public $data = array();
	
	public $type = 'html';
	
	public $theme = 'default';
	
	public $layout = 'default';
	
	public function render($path)
	{
		$this->data += array(
			'path' => $path,
			'type' => $this->type,
			'theme' => $this->theme,
		);
		$renderer = new \ephFrame\view\Renderer();
		$filename = APP_ROOT.'/view/'.$this->theme.'/'.$path.'.'.$this->type.'.php';
		$layout =  APP_ROOT.'/view/'.$this->theme.'/layout/'.$this->layout.'.'.$this->type.'.php';
		return $renderer->render($layout, $this->data + array('content' => $renderer->render($filename, $this->data)));
	}
	
	public function element($path, Array $data = array())
	{
		$filename = APP_ROOT.'/view/'.$this->theme.'/element/'.$path.'.'.$this->type.'.php';
		return $renderer->render($filename, $this->data + $data);
	}
}