<?php

namespace ephFrame\view;

class Renderer
{
	public $view;
	
	public $extension = 'php';

	public function render($__template, Array $data = array())
	{
		if (!empty($this->extension)) {
			$__template .= '.'.$this->extension;
		}
		if (!is_file($__template)) {
			throw new TemplateNotFoundException($__template);
		}
		extract($data, EXTR_OVERWRITE);
		ob_start();
		require $__template;
		return ob_get_clean();
	}
}

class RendererException extends \Exception {}

class TemplateNotFoundException extends RendererException
{
	public function __construct($filename)
	{
		return parent::__construct(sprintf('Template file "%s" could not be found.', $filename));
	}
}