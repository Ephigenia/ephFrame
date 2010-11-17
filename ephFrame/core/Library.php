<?php

namespace ephFrame\core;

class Library
{
	public static function load($path)
	{
		$path = '../..'.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $path).'.php';
		require $path;
	}
}

spl_autoload_register('\ephFrame\core\Library::load');