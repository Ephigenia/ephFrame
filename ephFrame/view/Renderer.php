<?php

namespace ephFrame\view;

class Renderer
{
	public function render($template, $data = array())
	{
		ob_start();
		extract($data);
		require $template;
		return ob_get_clean();
	}
}