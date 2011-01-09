<?php

namespace ephFrame\core;

class Library
{
	protected static $paths = array(
		'ephFrame' => '',
	);

	public static function add($namespace, $path)
	{
		if (!is_dir($path)) {
			throw new LibraryPathNotFoundException($path);
		}
		self::$paths[$namespace] = $path;
		return true;
	}

	public static function load($path)
	{
		foreach(self::$paths as $namespace => $libPath) {
			if (strncasecmp($path, $namespace, strlen($namespace)) === 0) {
				$path = $libPath.DIRECTORY_SEPARATOR.substr(str_replace('\\', DIRECTORY_SEPARATOR, $path), strlen($namespace)+1).'.php';
				break;
			}
		}	
		require $path;
	}
}

spl_autoload_register('\ephFrame\core\Library::load');
Library::add('ephFrame', realpath(dirname(__DIR__)));

class LibraryException extends \Exception {}

class LibraryPathNotFoundException extends LibraryException
{
	public function __construct($filename)
	{
		return parent::__construct(sprintf('Path "%s" could not be found.', $filename));
	}
}