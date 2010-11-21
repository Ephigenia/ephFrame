<?php

namespace app\lib\controller;

class Controller extends \ephFrame\core\Controller
{
	public function display($page)
	{
		die(var_dump(func_get_args()));
		$this->action = $page;
	}
}