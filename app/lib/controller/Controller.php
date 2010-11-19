<?php

namespace app\lib\controller;

class Controller extends \ephFrame\core\Controller
{
	public function display($page)
	{
		$this->action = $page;
	}
}