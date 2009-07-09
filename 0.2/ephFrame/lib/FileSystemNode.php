<?php

/**
 * ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

/**
 * File System Node
 * 
 * This class represents a file system node on the local filesystem 
 * and is mainly used by the child-classes {@link File} and {@link Directory}
 * for standard file/directory operations.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 12.07.2007
 * @package ephFrame
 * @abstract 
 * @subpackage ephFrame.lib
 */
abstract class FileSystemNode extends Object {
	
	/**
	 * Stores the name of this FileSystemNode
	 * @var string
	 */
	public $nodeName;
	
	/**
	 * Stores the regexp to check if a FileSystemNode is hidden or not
	 * @var string
	 */
	public $hiddenIndicator = '/^\./';
	
	/**
	 * 
	 */
	public function __construct($nodeName) {
		$nodeName = trim($nodeName);
		if (empty($nodeName)) throw new StringExpectedException();
		$this->nodeName = $nodeName;
		return $this;
	}
	
	/**
	 * Checks if the given file/directory name ot the nodeName of this
	 * file or directory indicates that the file/directory is hidden or
	 * not.
	 * @param string $name
	 * @return boolean
	 */
	public function isHidden($name = null) {
		if ($name === null) {
			$name = $this->nodeName;
		}
		if (empty($name)) {
			return false;
		} elseif (preg_match($this->hiddenIndicator, $name)) {
			return true;
		}
		return false;
	}
	
	public function __toString() {
		return $this->nodeName;
	}
	
	/**
	 * Chmods a file or returns the current chmod of file
	 * @param integer	$chmod
	 * @return File|integer
	 * @throws FileNotChmodableException
	 */
	public function chmod($chmod = null) {
		$this->checkExistence();
		if (func_num_args() > 0) {
			assert (in_array(strlen($chmod), array(3,4)));	
			if (!@chmod($this->nodeName, $chmod)) {
				throw new FileSystemNodeNotChmodableException($this);
			}
			return $this;
		} else {
			return fileperms($this->nodeName);
		}
	}
	
	/**
	 * Returns the owner of a file or sets the owner of a file
	 * @param string	$chmod
	 * @return FileSystemNode|string
	 * @throws FileNotChownableException if chown does not work
	 */
	public function chown($user = null) {
		$this->checkExistence();
		if (func_num_args() > 0 || $user !== null) {
			if (empty($user)) throw new StringExpectedException();	
			if (!@chown($this->nodeName, $user)) {
				throw new FileSystemNodeNotChmodableException($this);
			}
			return $this;
		} else {
			return fileowner($this->filename);
		}
	}
	
	/**
	 * This is an alias for {@link chown}
	 * @param string $owner
	 * @return File|string
	 */
	public function owner($owner = null) {
		return $this->chown($owner);
	}
	
	/**
	 * Checks if the file is readable
	 * @return boolean
	 */
	public function readable() {
		if (!$this->exists()) return false;
		return is_readable($this->nodeName);
	}
	
	/**
	 * Checks if a file is writables
	 * @return boolean
	 */
	public function writable() {
		return is_writable($this->nodeName);
	}
	
	/**
	 * Checks if th file exists
	 * @return boolean
	 */
	public function exists() {
		return (file_exists($this->nodeName));
	}
	
	public function checkExistence() {
		if (!$this->exists()) {
			throw new FileSystemNodeNotFoundException($this);
			return false;
		}
		return true;
	}
	
	/**
	 * Returns timestamp of the last modification if this file
	 * @return integer Timestamp of last modification
	 * @throws FileNotFoundException
	 */
	public function lastModified() {
		return filemtime($this->nodeName);
	}
	
	/**
	 * Returns timestamp of the creation of this file
	 * @return integer	Timestamp
	 * @throws FileNotFoundException
	 */
	public function created() {
		return filectime($this->nodeName);
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileSystemNodeException extends BasicException {}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileSystemNodeNotFoundException extends FileSystemNodeException {}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileSystemNodeNotChmodableException extends FileSystemNodeException  {
	public function __construct(FileSystemNode $node) {
		$this->message = 'Unable to chmod \''.$node->nodeName.'\'.';
		parent::__construct();
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileSystemNodeNotChownableException extends FileSystemNodeException {
	public function __construct(FileSystemNode $node) {
		$this->message = 'Unable to chown \''.$node->nodeName.'\'.';
		parent::__construct();
	}
}