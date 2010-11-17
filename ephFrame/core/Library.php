<?php

namespace ephFrame\core;

class Library
{
	public static function load($path)
	{
		if (strncasecmp($path, 'ephframe', 7) === 0) {
			$path = __DIR__.'/../../'.str_replace('\\', DIRECTORY_SEPARATOR, $path).'.php';
		} else {
			$path = '../..'.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $path).'.php';
		}
		require $path;
	}
}

spl_autoload_register('\ephFrame\core\Library::load');