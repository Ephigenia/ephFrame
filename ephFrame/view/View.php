<?php

namespace ephFrame\view;

class View
{
	public $data = array();
	
	public $type = 'html';
	
	public $layout = 'default';
	
	public $renderer;
	
	public $rootPath;
	
	public function __construct(Renderer $Renderer = null)
	{
		$this->renderer = $Renderer ?: new \ephFrame\view\Renderer();
		$this->rootPath = APP_ROOT.'/view/';
	}
	
	public function render($part, $path, Array $data = array())
	{
		// add current view information to view variables to use in the view
		$this->data += array(
			'path' => $path,
		);
		$this->renderer->view = $this; //@todo clear this
		switch($part) {
			default:
			case 'view':
				return $this->renderer->render($this->rootPath.DIRECTORY_SEPARATOR.$path.'.'.$this->type.'.php', $this->data + $data);
				break;
			case 'layout':
				return $this->render(false, 'layout/'.$path, $this->data + $data);
			case 'element':
				return $this->render(false, 'element/'.$path, $this->data + $data);
			case 'all':
				return $this->render('layout', $this->layout, array(
					'content' => $this->render('view', $path)
				));
		}
	}
}