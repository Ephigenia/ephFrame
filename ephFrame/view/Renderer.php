<?php

namespace ephFrame\view;

class Renderer
{
	protected $view;
	
	public function render($template, Array $data = array())
	{
		if (!is_file($template)) {
			throw new TemplateNotFoundException($template);
		}
		$this->data = $data;
		extract($data, EXTR_OVERWRITE);
		ob_start();
		require $template;
		return ob_get_clean();
	}
	
	protected function element($path, Array $data = array())
	{
		return $this->view->render('element', $path, $this->data + $data);
	}
	
}

class RendererException extends \Exception {}

class TemplateNotFoundException extends RendererException
{
	public function __construct($filename) {
		return parent::__construct(sprintf('Template file "%s" could not be found.', $filename));
	}
}