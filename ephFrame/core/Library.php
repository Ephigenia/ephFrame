<?php

namespace ephFrame\core;

class Library
{
	protected static $paths = array(
		'ephFrame' => '',
	);
	
	public static function add($namespace, $path)
	{
		self::$paths[$namespace] = $path;
	}
	
	public static function load($path)
	{
		foreach(self::$paths as $namespace => $libPath) {
			if (strncasecmp($path, $namespace, strlen($namespace)) === 0) {
				$path = dirname($libPath).DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $path).'.php';
				break;
			}
		}
		require $path;
	}
}

spl_autoload_register('\ephFrame\core\Library::load');
Library::add('ephFrame', realpath(dirname(__DIR__)));