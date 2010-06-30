<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

// load classes needed for this class
class_exists('FileSystemNode') or require dirname(__FILE__).'/FileSystemNode.php';
class_exists('IndexedArray') or require dirname(__FILE__).'/IndexedArray.php';
class_exists('ArrayHelper') or require dirname(__FILE__).'/helper/ArrayHelper.php';

/**
 * Directory Class
 * 
 * This class is for creating, deleting and manipulating directories
 * on the local filesystem.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 06.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @version 0.2
 * @uses IndexedArray
 * @uses File
 * @uses ArrayHelper
 */
class Dir extends FileSystemNode 
{
	/**
	 * Stores the path to this directory
	 * @var string
	 */
	public $path;
	
	public $listHiddenFiles = false;
	public $listHiddenDirectories = false;
	
	/**
	 * Directory Constructo$newFilename = $realFile->basename(false).'.'.$realFile->extension();r
	 * missing trailing / are added
	 * @param string $path
	 * @return Directory
	 */
	public function __construct($path) 
	{
		if (is_object($path) && is_subclass_of($path, 'Controller')) {
			return parent::__construct($path);
		}
		assert(is_string($path));
		if (!empty($path) && $path !== '.' && !$path !== '..' && substr($path, -1, 1) != DS) {
			$path .= DS;
		}
		parent::__construct($path);
		if (empty($path)) throw new StringExpectedException();
		$this->path = $path;
	}
	
	/**
	 * Tries to create a new file with $filename and $content.
	 * @param string $filename
	 * @param string $content
	 * @param string $class
	 * @return File
	 */
	public function newFile($filename, $content = null, $class = 'File') 
	{
		$newFilename = $this->nodeName.$filename;
		if (!$this->writable()) {
			try {
				$this->chmod(0777);
			} catch (FileSystemNodeNotChmodableException $e) {
				throw new DirNotWritableException($this);
			}
		}
		file_put_contents($newFilename, $content);
		return new $class($newFilename);
	}
	
	/**
	 * @return boolean
	 */
	public function isDir() 
	{
		return true;
	}
	
	/**
	 * @return boolean
	 */
	public function isFile() 
	{
		return false;
	}
	
	/**
	 * Tests wheter this directory exists and is a directory
	 * @return boolean
	 */
	public function exists() 
	{
		return (parent::exists() && is_dir($this->path));
	}
	
	/**
	 * Test if the directory exists and if not throws an exception
	 * @return boolean
	 */
	public function checkExistence() 
	{
		if (!$this->exists()) {
			throw new DirNotFoundException($this->nodeName);
			return false;
		}
		return true;
	}
	
	/**
	 * Returns the number of files in this directory
	 * @return integer
	 */
	public function numFiles() 
	{
		return count($this->listFiles());
	}
	
	/**
	 * Returns the directory name
	 * <code>
	 * $dir = new Dir('img/users/');
	 * // echoes 'users'
	 * echo $dir->dirName();
	 * </code>
	 * @return string
	 */
	public function dirName() 
	{
		return dirname($this->nodeName);
	}
	
	/**
	 * Returns the number of directories in this directory
	 * @return integer
	 */
	public function numDirectories() 
	{
		return count($this->listDirectories());
	}
	
	/**
	 * Returns Files in this directory
	 * @return Array(File) of Files
	 */
	public function listFiles($pattern = null) 
	{
		return ArrayHelper::extractByClassName($this->read($pattern), 'File');
	}
	
	/**
	 * Returns Directories that are in this directory
	 * @return Array(Dir) of Directories
	 */
	public function listDirectories($pattern = null) 
	{
		return ArrayHelper::extractByClassName($this->read($pattern), get_class($this));
	}
	
	/**
	 * Array of files in a directory.
	 * 
	 * Returns a {@link IndexedArray} of {@link File} in this directory.
	 * 
	 * This method gets you also the hidden files and directories if the
	 * flags {@link listHiddenFiles} and {@link listHiddenDirectories} are on.
	 * Prevent listing hidden files by setting them to false.
	 * 
	 * $pattern should be a regular expression starting with @ to filter files
	 * and directories:
	 * <code>
	 * $dir = new Dir('../tmp/');
	 * foreach($dir->read('@(jpg|gif|png)$@i') as $found) {
	 * 	echo $found->nodeName;
	 * }
	 * </code>
	 * 
	 * @param string $pattern regular expression (delimeter should be @) or simple file search like *.jpg
	 * @return IndexedArray {@link IndexedArray} of files and directories
	 * @throws DirNotFoundException if directory was not found
	 */
	public function read($pattern = null) 
	{
		$this->checkExistence();
		$contents = new IndexedArray();
		foreach (scandir($this->nodeName) as $possible) {
			// create either file or directory objects depending on found
			if (!empty($pattern) && !preg_match($pattern, $possible)) continue;
			if (is_file($this->nodeName.$possible)
				&& (($this->listHiddenFiles)
				|| (!$this->listHiddenFiles && substr($possible, 0, 1) !== '.'))
				) {
				$contents->add(new File($this->nodeName.$possible));
			} elseif (!in_array($possible, array('.', '..'))
				&& is_dir($this->nodeName.$possible)
				&& (($this->listHiddenDirectories)
				|| (!$this->listHiddenDirectories && substr($possible, 0, 1) !== '.'))) {
				$contents->add(new Dir($this->nodeName.$possible));
			}
		}
		return $contents;
	}
	
	/**
	 * Create a new subdirectory or create this directory in the filesystem.
	 * 
	 * @param string|array(string) $directoryName
	 * @param integer $chmod
	 * @return Dir Instance of the newly created Directory
	 */
	public function create($newDirname = null, $chmod = 0777) 
	{
		if (is_array($newDirname)) {
			$newDirname = implode(DS, $newDirname);
		}
		if (is_dir($this->nodeName.$newDirname)) {
			return new Dir($this->nodeName.$newDirname);
		}
		$folder = explode(DS, rtrim($this->nodeName.$newDirname, DS));
		$mkfolder = '';
		for ($i = 0; isset($folder[$i]); $i++) {
			$mkfolder .= $folder[$i];
			if(!is_dir($mkfolder)) {
        		if (!@mkdir($mkfolder, $chmod, true)) {
        			throw new DirCreateErrorException($mkfolder);
        		}
        		chmod($mkfolder, $chmod);
			}
    		$mkfolder .= DS;
    	}
    	$mkfolder = preg_replace('@'.$mkfolder.'+@', DS, $mkfolder);
    	return new Dir($mkfolder);
	}
	
	/**
	 * Alias for {@link makeDir}
	 * @param string|array(string) $directoryName
	 * @param integer $chmod
	 * @return DirectoryComponent Instance of the newly created Directory
	 */
	public function mkdir($newDirname, $chmod = 0777) 
	{
		return $this->create($newDirname, $chmod);
	}
	
	/**
	 * recursivly delete directory passed or current directory
	 * @param string $path
	 * @return boolean
	 */
	public function delete($path = null) 
	{
		if (func_num_args() == 0) {
			$dir = $this;
		} else {
			$dir = new Dir($path);
		}
		if (!$dir->exists()) return false;
		$dir->listHiddenFiles = true;
		foreach(array_merge($dir->listFiles(), $dir->listDirectories()) as $Obj) {
			$Obj->delete();
		}
		return rmdir($this->nodeName);
	}
	
	/**
	 * Returns the Size of a directory in bytes rekursive if wanted
	 * @param boolean $rekursive
	 * @param boolean $humanized Returned size is 'humanized'
	 * @return integer
	 */
	public function size($rekursive = true, $humanized = false) 
	{
		$this->checkExistence();
		$size = 0;
		if($handle = opendir($this->dirname)) {
			while(($file = readdir($handle)) !== false) {
				if ($file !== '.' && $file !== '..') {
					$path = $this->dirname.$file;
					if (is_file($path)) {
						$size += filesize($path);
					} elseif (is_dir($path) && $rekursive) {
						$dir = new DirectoryComponent($path);
						$size += $dir->size(true);
					}
				}
			}
		}
		if (!$humanized) {
			return $size;
		} else {
			return File::sizeHumanized($size);
		}
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DirException extends FileSystemNodeException 
{}

/**
 * Thrown as soon a directory is needed but not existant.
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DirNotFoundException extends DirException 
{
	public function __construct($directory = null) 
	{
		$this->message = 'Directory not found.';
		if ($directory !== null) {
			if (is_object($directory)) {
				$this->message = 'Directory not found: \''.$directory->nodeName.'\'.';
			} else {
				$this->message = 'Directory not found: \''.$directory.'\'.';
			}
		}
		parent::__construct();
	}
}

/**
 * Thrown as soon a directory is needed but not existant.
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DirNotWritableException extends DirException 
{
	public function __construct($directory) 
	{
		if ($directory !== null) {
			if (is_object($directory)) {
				$this->message = sprintf('The directory \'%s\' is not writable.', $directory->nodeName);
			} else {
				$this->message = sprintf('The directory \'%s\' is not writable.', $directory);
			}
		}
		parent::__construct();
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DirCreateErrorException extends DirException 
{
	public function __construct($dirname) 
	{
		parent::__construct('Unable to create directory \''.$dirname.'\'');
	}
}