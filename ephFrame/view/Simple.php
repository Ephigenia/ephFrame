<?php

namespace ephFrame\view;

use \ephFrame\view\Renderer;

class Simple extends Renderer
{
	public function render()
	{
		$ret = parent::render();
		foreach($this->data as $k => $v) {
			$ret = preg_replace('@:'.preg_quote($k, '@').'@', $v, $ret);
		}
		return $ret;
	}
}