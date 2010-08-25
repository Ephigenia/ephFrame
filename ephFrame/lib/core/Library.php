<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

class_exists('ClassPath') or require dirname(__FILE__).'/ClassPath.php';

/**
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2010-08-24
 * @package ephFrame
 * @subpackage ephFrame.core
 */
class Library
{
	/**
	 * Loads a class, syntax is like in flash or javascript applications:
	 * <code>
	 * // load XML Component Class
	 * Library::loadClass("ephFrame.lib.component.XML");
	 * // load a class within the application
	 * Library::loadClass("app.lib.ownClass");
	 * </code>
	 * 
	 * It't not possible to load classes that are out of the document root by
	 * checking for ../ oder / or all / characters
	 * 
	 * @throws LibraryFileNotFoundException
	 * @param string $path
	 * @return true
	 */
	public static function load($path)
	{
		$classname = ClassPath::className($path);
		if (!class_exists($classname)) {
			$translatedPath = ClassPath::translatePath($path);
			if (!ClassPath::exists($path)) {
				throw new LibraryFileNotFoundException($path);
			}
			require $translatedPath;
		}
		return $classname;
	}
	
	/**
	 * Create a new instance of an object passing max. 4 elemnts of $arguments
	 * to the constructor.
	 * 
	 * @param string $path
	 * @param array(string)
	 * @return Object
	 */
	public static function create($path, Array $arguments = array())
	{
		$classname = self::load($path);
		switch(count($arguments)) {
			case 0:
				return new $classname();
				break;
			default:
			case 1:
				return new $classname($arguments[0]);
				break;
			case 2:
				return new $classname($arguments[0], $arguments[1]);
				break;
			case 3:
				return new $classname($arguments[0], $arguments[1], $arguments[2]);
				break;
			case 4:
				return new $classname($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
				break;
		}
	}
}


/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class LibraryException extends ephFrameException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class LibraryFileNotFoundException extends LibraryException 
{
	public function __construct($classPath) 
	{
		if (empty($classPath)) {
			$classPath = '[empty]';
		}
		$this->message = sprintf('Sorry i was unable to find the class file "%s"', $classPath);
		parent::__construct($this->message);
	}
}