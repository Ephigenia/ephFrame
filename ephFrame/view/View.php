<?php

namespace ephFrame\view;

class View
{
	public $data = array();
	
	public $layout = 'default';
	
	public $renderer;
	
	public $rootPath;
	
	public $type = 'html';
	
	public function __construct(Renderer $Renderer = null)
	{
		$this->renderer['default'] = $Renderer ?: new \ephFrame\view\Renderer();
		$this->rootPath = APP_ROOT.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR;
	}
	
	protected function renderer($part)
	{
		if (isset($this->renderer[$part])) {
			$renderer = $this->renderer[$part];
		} else {
			$renderer = $this->renderer['default'];
		}
		$renderer->view = $this; //@todo clear this
		return $renderer;
	}
	
	public function render($part, $path, Array $data = array())
	{
		// add current view information to view variables to use in the view
		$this->data += array(
			'path' => $path,
		);
		$renderer = $this->renderer($part, $path, $data);
		switch($part) {
			default:
			case 'view':
				return $renderer->render($this->rootPath.$path.'.'.$this->type, $this->data + $data);
				break;
			case 'layout':
				return $this->render(false, 'layout/'.$path, $this->data + $data);
			case 'element':
				return $this->render(false, 'element/'.$path, $this->data + $data);
			case 'all':
				return $this->render('layout', $this->layout, array(
					'content' => $this->render('view', $path)
				) + $data);
		}
	}
}