<?php

namespace ephFrame\view;

use ephFrame\view\Element;

class View
{
	public $data = array();
	
	protected $extension = 'php';
	
	public function render($name)
	{
		ob_start();
		require APP_ROOT.'/view/default/app/'.$name.'.php';
		return ob_get_clean();
	}
	
	public function element($name, Array $data)
	{
		return new Element($name, $data);
	}
}