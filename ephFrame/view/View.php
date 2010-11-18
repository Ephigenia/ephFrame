<?php

namespace ephFrame\view;

use ephFrame\view\Element;

class View
{
	protected $renderer;
	
	public $data = array();
	
	public function render($action, Array $data = array())
	{
		$this->renderer = new Simple('default/app/'.$action, $data);
		return $this->renderer->render();
	}
	
	public function element($name, Array $data)
	{
		return new Element($name, $data);
	}
}