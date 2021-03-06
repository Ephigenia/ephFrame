<?php

namespace ephFrame\view;

class View extends \ArrayObject
{
	public $layout = 'default';
	
	public $renderer;
	
	public $rootPath;
	
	public $type = 'html';
	
	public function __construct(Renderer $Renderer = null, Array $data = array())
	{
		$this->renderer['default'] = $Renderer ?: new \ephFrame\view\Renderer();
		$this->rootPath = APP_ROOT.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR;
		if (isset($data['layout'])) {
			$this->layout = $data['layout'];
		}
		if (isset($data['rootPath'])) {
			$this->rootPath = $data['rootPath'];
		}
		return parent::__construct($data, \ArrayObject::ARRAY_AS_PROPS);
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
		$this->path = $path;
		$renderer = $this->renderer($part, $path, $data);
		switch($part) {
			default:
			case 'view':
				return $renderer->render($this->rootPath.$path.'.'.$this->type, $data + (array) $this);
				break;
			case 'layout':
				return $this->render(false, 'layout/'.$path, $data + (array) $this);
			case 'element':
				return $this->render(false, 'element/'.$path, $data + (array) $this);
			case 'all':
				$content = $this->render('view', $path, $data + (array) $this);
				if ($this->layout) {
					return $this->render('layout', $this->layout, $data + array('content' => $content));
				}
				return $content;
		}
	}
}