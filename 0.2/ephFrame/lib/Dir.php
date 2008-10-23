<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright 2007+, Ephigenia M. Eichner, Kopernikusstr. 8, 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 	@license http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright Copyright 2007+, Ephigenia M. Eichner
 * 	@link http://code.ephigenia.de/projects/ephFrame/
 * 	@filesource
 */

// load classes needed for this class
require_once dirname(__FILE__).'/FileSystemNode.php';
require_once dirname(__FILE__).'/File.php';
ephFrame::loadClass('ephFrame.lib.Set');
ephFrame::loadClass('ephFrame.lib.helper.ArrayHelper');

/**
 *	Directory Class
 * 
 * 	This class is for creating, deleting and manipulating directories
 * 	on the local filesystem.
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 06.05.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@version 0.2
 * 	@uses Set
 * 	@uses File
 * 	@uses ArrayHelper
 */
class Dir extends FileSystemNode {
	
	/**
	 *	Stores the path to this directory
	 * 	@var string
	 */
	public $path;
	
	public $listHiddenFiles = false;
	public $listHiddenDirectories = false;
	
	/**
	 * 	Directory Constructor
	 * 	missing trailing / are added
	 * 	@param string $path
	 * 	@return Directory
	 */
	public function __construct($path) {
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
		if (!$this->exists()) throw new DirNotFoundException();
	}
	
	/**
	 * 	@return boolean
	 */
	public function isDir() {
		return true;
	}
	
	/**
	 * 	@return boolean
	 */
	public function isFile() {
		return false;
	}
	
	/**
	 *	Tests wheter this directory exists and is a directory
	 * 	@return boolean
	 */
	public function exists() {
		return (parent::exists() && is_dir($this->path));
	}
	
	/**
	 *	Test if the directory exists and if not throws an exception
	 * 	@return boolean
	 */
	public function checkExistence() {
		if (!$this->exists()) {
			throw new DirNotFoundException($this->nodeName);
			return false;
		}
		return true;
	}
	
	/**
	 * 	Returns the number of files in this directory
	 * 	@return integer
	 */
	public function numFiles() {
		return count($this->listFiles());
	}
	
	/**
	 *	Returns the directory name
	 * 	<code>
	 * 	$dir = new Dir('img/users/');
	 * 	// echoes 'users'
	 * 	echo $dir->dirName();
	 * 	</code>
	 * 	@return string
	 */
	public function dirName() {
		return dirname($this->nodeName);
	}
	
	/**
	 *	Returns the number of directories in this directory
	 * 	@return integer
	 */
	public function numDirectories() {
		return count($this->listDirectories());
	}
	
	/**
	 *	Returns Files in this directory
	 * 	@return Array(File) of Files
	 */
	public function listFiles() {
		return ArrayHelper::extractByClassName($this->contents(), 'File');
	}
	
	/**
	 *	Returns Directories that are in this directory
	 * 	@return Array(Dir) of Directories
	 */
	public function listDirectories() {
		return ArrayHelper::extractByClassName($this->contents(), get_class($this));
	}
	
	/**
	 * 	Array of files in a directory.
	 * 
	 *	Returns a {@link Set} of {@link File} in this directory.
	 * 
	 * 	This method gets you also the hidden files and directories if the
	 * 	flags {@link listHiddenFiles} and {@link listHiddenDirectories} are on.
	 *	Prevent listing hidden files by setting them to false.
	 * 
	 * 	@param boolean $hiddenStuff show hidden files and directories?
	 * 	@return Set {@link Set} of files and directories
	 */
	public function read() {
		$dh = scandir($this->nodeName, 1);
		$contents = new Set();
		foreach ($dh as $possible) {
			// create either file or directory objects depending on found
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
	 *	Create directories rekursive
	 * 	@param string|array(string) $directoryName
	 * 	@param integer $chmod
	 * 	@return Dir Instance of the newly created Directory
	 */
	public function makeDir($newDirname, $chmod = 0777) {
		if (is_array($newDirname)) {
			$newDirname = implode(DS, $newDirname);
		}
		// return newDirname directory component if directory allread exists
		if (is_dir($this->dirname.$newDirname)) {
			return new Dir($this->dirname.$newDirname);
		}
		$folder = explode(DS, $this->dirname.$newDirname);
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
    	return new Dir($newDirname);
	}
	
	/**
	 *	Alias for {@link makeDir}
	 * 	@param string|array(string) $directoryName
	 * 	@param integer $chmod
	 * 	@return DirectoryComponent Instance of the newly created Directory
	 */
	public function mkdir($newDirname, $chmod = 0777) {
		return $this->makeDir($newDirname, $chmod);
	}
	
	/**
	 *	Returns the Size of a directory in bytes rekursive if wanted
	 * 	@param boolean $rekursive
	 * 	@param boolean $humanized Returned size is 'humanized'
	 * 	@return integer
	 */
	public function size($rekursive = true, $humanized = false) {
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
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class DirException extends FileSystemNodeException {}

/**
 * 	Thrown as soon a directory is needed but not existant.
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class DirNotFoundException extends DirException {
	public function __construct($directory = null) {
		$this->message = 'Directory not found.';
		if ($directory !== null) {
			if (is_object($directory)) {
				$this->message = 'Unable to find directory \''.$directory->nodeName.'\'.';
			} else {
				$this->message = 'Unable to find directory \''.$directory.'\'.';
			}
		}
		parent::__construct();
	}
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class DirCreateErrorException extends DirException {
	public function __construct($dirname) {
		parent::__construct('Unable to create directory \''.$dirname.'\'');
	}
}
?>