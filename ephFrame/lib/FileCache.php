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

/**
 * Caching engine that stores cached stuff in files and directories
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 25.09.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 */
class FileCache extends Object implements CacheEngine 
{
	public $directory;	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileCacheException extends BasicException 
{}