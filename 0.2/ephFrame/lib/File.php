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

// load parent class
require_once dirname(__FILE__).'/FileSystemNode.php';

/**
 *	File Class
 * 
 * 	This class should represent files. You can copy them, move them, check
 * 	their existence, check the size, get the Mime-Type and various other stuff.
 * 	Check the examples in the methods.
 * 	
 * 	See the method list and example usages for more examples.
 * 
 * 	This class is partially tested by {@link TestFile}.
 * 
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@version 0.2
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 10.05.2007
 * 	@uses MimeTypes
 */
class File extends FileSystemNode {
	
	/**
	 *	Read Buffer size on {@link read}ing actions
	 * 	@var integer
	 */
	private $readBufferSize = 1024;
	
	/**
	 * 	File Constructor
	 * 	@param string $filename
	 * 	@return File
	 */
	public function __construct($filename = null) {
		parent::__construct($filename);
		return $this;
	}
	
	/**
	 * 	Returns if true if this object has a filename and the file exists,
	 * 	otherwise always true
	 * 	@return boolean
	 */
	public function isFile() {
		if ($this->exists()) {
			return is_file($this->nodeName);
		}
		return true;
	}
	
	/**
	 * 	Returns always false
	 * 	@return boolean
	 */
	public function isDir() {
		return false;
	}
	
	/**
	 *	Tests if the file is uploaded or not
	 * 	@return boolean
	 */
	public function isUploaded() {
		if (!$this->exists()) return false;
		return is_uploaded_file($this->nodeName);
	}
	
	/**
	 *	Returns the complete path to this file
	 * 	@return string
	 */
	public function filename() {
		return $this->nodeName;
	}

	/**
	 *	Returns just the basename for this file, this is like 
	 * 	basname() in php. You can pass true as second argument for getting
	 * 	the file's name without extension.
	 * 	<code>
	 * 	$file = new File('app/webroot/img/test.jpg');
	 * 	// will echo 'test'
	 * 	echo $file->basename(true);
	 * 	// will echo 'test.jpg';
	 * 	echo $file->basename();
	 * 	</code>
	 * 	@return string
	 */
	public function basename($cutExtension = false) {
		if ($cutExtension) {
			return substr(basename($this->filename()), 0, -strlen($this->extension())-1);	
		} else {
			return basename($this->filename());
		}
	}
	
	/**
	 * 	Deletes this file if existent
	 * 	@return boolean
	 */
	public function delete() {
		if (!$this->exists()) return true;
		if (@unlink($this->nodeName)) {
			return true;
		}
		return false;
	}
	
	/**
	 *	Checks if the file exists in the filesystem and is really a file
	 * 	@return boolean
	 */
	public function exists() {
		return (parent::exists() && is_file($this->nodeName));
	}
	
	/**
	 * 	Creates an array of header messages for a download link for the file
	 * 	
	 * 	@param string $filename
	 * 	@return array
	 */
	public function createDownloadHeader($filename = null) {
		return array('Content-type: application/octet-stream',
			'Content-Length: '.$this->size(null),
			'Content-Disposition: attachment; filename="'.basename($filename !== null ? $filename : $this->filename()) . '"'
		);
	}
	
	/**
	 *	Checks if this file exists or not. If the file does not exist a
	 * 	{@link FileNotFoundException} is thrown
	 * 	@return boolean
	 */
	public function checkExistence() {
		if (empty($this->nodeName)) {
			throw new FileEmptyException($this);
		}
		if (!$this->exists()) {
			throw new FileNotFoundException($this);
			return false;
		}
		return true;
	}
	
	/**
	 * 	Moves the file to an other location
	 * 	@param string $newName
	 * 	@return File
	 */
	public function move($newName) {
		$this->checkExistence();
		// check directory existence
		$dir = new Dir(dirname($newName));
		$dir->checkExistence();
		if ($this->isUploaded()) {
			move_uploaded_file($this->nodeName, $newName);	
		} else {
			rename($this->nodeName, $newName);
		}
		$className = get_class($this);
		return new $className($newName);
	}
	
	/**
	 *	Copies this file to an other location and returns a File Instance of
	 * 	the new file if the copy action succeeds
	 * 	@param string $newName
	 * 	@return File
	 */
	public function copy($newName) {
		$this->checkExistence();
		// test if aiming directory is existent
		$dir = new Dir(dirname($newName));
		$dir->checkExistence();
		copy($this->nodeName, $newName);
		$className = get_class($this);
		return new $className($newName);
	}
	
	/**
	 * 	Returns the file extension of the File if there's any
	 * 	@return string
	 */
	public function extension() {
		return File::ext($this->nodeName);
	}
	
	/**
	 *	Returns the file extentions from a $filename.
	 * 
	 * 	Empty extensions will result in a false as return value.
	 * 
	 * 	This method is multi-byte save beacuse the file/extension devider is a
	 * 	single byte character.
	 * 
	 * 	@param string $filename
	 * 	@return string|boolean false if no extension is found
	 * 	@static
	 */
	public static function ext($filename) {
		if (($pos = strrpos($filename, '.')) === false) {
			return false;
		} else {
			return trim(substr($filename, $pos + 1));
		}
	}
	
	/**
	 *	Returns the mime type of this file if found in {@link MimeTypes}
	 * 	@return string
	 */
	public function mimeType() {
		if (!($extension = $this->extension())) return false;
		loadClass('ephFrame.lib.helper.MimeTypes');
		return MimeTypes::mimeType($this->nodeName);
	}
	
	/**
	 *	Tests if this file has any extension (marked by the last .)
	 * 	@return boolean
	 */
	public function hasExtension() {
		return strrpos(basename($this->nodeName), '.');
	}
	
	/**
	 * 	Returns the filesize in Bytes or a string of the filesize as in human
	 * 	readable format using {@link sizeHumanized}
	 * 	@param integer $precision
	 * 	@param boolean $humanized
	 * 	@return integer|string
	 */
	public function size($precision = 2, $humanized = false) {
		if (!$this->exists()) throw new FileNotFoundException($this);
		if ($humanized) {
			return self::sizeHumanized(filesize($this->nodeName), $precision);
		} else {
			return filesize($this->nodeName);
		}
	}
	
	/**
	 * 	Alias for {@link size}
	 *	@param integer $precision
	 * 	@param boolean $humanized
	 * 	@return integer|string
	 */
	public function filesize() {
		return $this->size($precision = 2, $humanized = false);
	}
	
	/**
	 * 	Returns the filesize in a human readable format
	 * 	@param integer $size
	 * 	@param integer $precision
	 * 	@return string
	 */
	public static function sizeHumanized($size, $precision = 2) {
		if ($size <= 0) return '0 KB';
		$position = 0;
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		while($size >= 1024 && ($size / 1024.) >= 1) {
			$size /= 1024.;
			$position++;
		}
		if (round($size, $precision) == 1024) {
			$size /= 1024;
			$position++;
		}
		return round($size, $precision).' '.$units[$position];
	}
	
	/**
	 * 	Tries to translate 32M or 34KB or even 2324 TB into the equivalent
	 * 	byte size and returns it.
	 * 	@param string $size
	 * 	@return integer
	 */
	public static function deHumanizeFileSize($size) {
		preg_match('/^([0-9]+)\s*(M|MB|KB|K|B|G|T)$/', $size, $found);
		if (!isset($found[1])) {
			return false;
		}
		$num = $found[1];
		if (isset($found[2])) {
			switch($found[2]) {
				default:
				case 'B':
					break;
				case 'M':
				case 'MB':
					$num *= MEGABYTE;
					break;
				case 'KB':
					$num *= KILOBYTE;
					break;
				case 'TB':
					$num *= TERRABYTE;
					break;
				case 'GB':
					$num *= GIGABYTE;
					break;
			}
		}
		return $num;
	}
	
	/**
	 *	Appends $string to a file
	 * 
	 * 	<code>
	 * 	$log = new File('log.txt');
	 * 	$log->append('someone logged something important');
	 * 	</code>
	 * 	
	 * 	@param string
	 * 	@return File
	 */
	public function append($string) {
		if (!$this->exists()) $this->create();
		if (!$this->readable()) throw new FileNotFoundException($this);
		$fp = fopen($this->nodeName, 'a');
		fputs($fp, $string);
		fclose($fp);
		return $this;
	}
	
	/**
	 *	Read contents of the file
	 * 	
	 * 	This reads the file into an array ($asArray = true) or as string.
	 * 	<code>
	 * 	$file = new File('log.txt');
	 * 	foreach($line = $file->read(true)) {
	 * 		echo $line.'<br />;
	 * 	}
	 * 	</code>
	 * 	@param boolean $asArray Read file into an array instead of a string
	 * 	@param integer $readBufferSize Custom read buffer size
	 * 	@return array(string)|string
	 */
	public function read($asArray = false, $readBufferSize = null) {
		if ($readBufferSize === null) {
			$readBufferSize = $this->readBufferSize;
		}
		$this->checkExistence();
		if (!$this->readable()) throw new FileNotReadableException($this);
		if ($asArray) {
			$return = file($this->filename());
		} else {
			$return = file_get_contents($this->filename());
		}
		return $return;
	}
	
	/**
	 *	Write to a file
	 * 	
	 * 	Write $content to a file. If the file does not exists the class will
	 * 	try to create it.
	 * 	@param string
	 * 	@return File
	 */
	public function write($content) {
		if (!$this->exists()) {
			$fp = $this->create();
		}
		$fp = fopen($this->filename, 'w');
		fputs($fp, $content, strlen($content));
		fclose($fp);
		return true;
	}
	
	/**
	 *	Creates a file with the given name or re-creates the current file
	 * 	@param string $filename filename of a new file to create
	 * 	@param integer $chmod chmod of the new file
	 * 	@return boolean success
	 */
	public function create($filename = null, $chmod = 0644) {
		if ($filename === null) {
			$fp = @fopen($this->nodeName, 'w');	
		} else {
			assert(is_string($filename) && strlen($filename) > 0);
			$fp = @fopen($this->nodeName, 'w');
		}
		if ($filename == null) {
			$file = $this;
		} else {
			$file = new File($filename);
		}
		if (!$fp) {
			throw new FileNotWriteableException($file);
		}
		$file->chmod($chmod);
		return $file;
	}
	
	/**
	 *	Checks a the file for possible php tags, this is used
	 * 	by some site attacks, that upload images with php code
	 * 	@throws FileWithPHPTagsException
	 */
	public function checkForPHPTags() {
		$this->checkExistence();
		if (!$this->readable()) throw new FileNotReadableException($this);
		$fp = fopen($this->nodeName, 'b');
		while(!feof($fp)) {
			$line = fgets($fp, $this->readBufferSize);
			if (preg_match('', $line)) throw new FileWithPHPTagsException($this);
		}
		fclose($fp);
		return $this;
	}

}

/**
 * 	Basic File Exception
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class FileException extends FileSystemNodeException {
	
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class FileNotFoundException extends FileException {
	public function __construct(File $file) {
		parent::__construct('Unable to find file \''.$file->nodeName.'\'.');
	}
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class FileNotReadableException extends FileException {
	public function __construct(File $file) {
		parent::__construct('Unable to read from file \''.$file->nodeName.'\'.');
	}
}

/**
 *	Thrown if a file is not writable or can not be created
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class FileNotWriteableException extends FileException {
	public function __construct(File $file) {
		parent::__construct('Unable to write file \''.$file->nodeName.'\'.');
	}
}

/**
 *	Basic File Intrusion Detection
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class FileWithPHPTagsException extends IntrusionException  {
	
}

?>