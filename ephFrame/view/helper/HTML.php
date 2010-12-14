<?php

namespace ephFrame\view\helper;

class HTML extends \ephFrame\view\Helper
{
	public function tag($name, $content, $attributes)
	{
		return new HTMLTag($tagName, $attributes, $content);
	}
}