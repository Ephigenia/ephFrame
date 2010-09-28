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

class_exists('FileSystemNode') or require dirname(__FILE__).'/FileSystemNode.php';
class_exists('Dir') or require dirname(__FILE__).'/Dir.php';

/**
 * File Class
 * 
 * This class is partially tested by {@link TestFile}.
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @version 0.2
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 10.05.2007
 * @uses MimeTypes
 */
class File extends FileSystemNode 
{	
	/**
	 * Read Buffer size on {@link read}ing actions
	 * @var integer
	 */
	protected $readBufferSize = 1024;
	
	/**
	 * Opened stream of a file
	 * @var ressource
	 */
	protected $stream;
	
	/**
	 * File Constructor
	 * @param string $filename
	 * @return File
	 */
	public function __construct($filename = null) 
	{
		parent::__construct($filename);
		return $this;
	}
	
	/**
	 * Returns if true if this object has a filename and the file exists,
	 * otherwise always true
	 * @return boolean
	 */
	public function isFile() 
	{
		if ($this->exists()) {
			return is_file($this->nodeName);
		}
		return true;
	}
	
	/**
	 * Returns always false
	 * @return boolean
	 */
	public function isDir() 
	{
		return false;
	}
	
	/**
	 * Tests if the file is uploaded or not
	 * @return boolean
	 */
	public function isUploaded() 
	{
		if (!$this->exists()) return false;
		return is_uploaded_file($this->nodeName);
	}
	
	/**
	 * Returns the complete path to this file
	 * @return string
	 */
	public function filename() 
	{
		return $this->nodeName;
	}

	/**
	 * Returns just the basename for this file, this is like 
	 * basname() in php. You can pass true as second argument for getting
	 * the file's name without extension.
	 * <code>
	 * $file = new File('app/webroot/img/test.jpg');
	 * // will echo 'test'
	 * echo $file->basename(false);
	 * // will echo 'test.jpg';
	 * echo $file->basename();
	 * </code>
	 * @return string
	 */
	public function basename($includeExtension = true) 
	{
		if (!$includeExtension) {
			return substr(basename($this->filename()), 0, -strlen($this->extension())-1);	
		} else {
			return basename($this->filename());
		}
	}
	
	/**
	 * Deletes this file if existent
	 * @return boolean
	 */
	public function delete() 
	{
		if (!$this->exists()) return true;
		if (@unlink($this->nodeName)) {
			return true;
		}
		return false;
	}
	
	/**
	 * Checks if the file exists in the filesystem and is really a file
	 * @return boolean
	 */
	public function exists() 
	{
		return is_file($this->nodeName);
	}
	
	/**
	 * Creates an array of header messages for a download link for the file
	 * You can send these headers by using array_map on the method’s result:
	 * <code>
	 * array_map('header', $file->createDownloadHeader());
	 * </code>
	 * @param string $filename
	 * @return array
	 */
	public function createDownloadHeader($filename = null) 
	{
		return array('Content-type: application/octet-stream',
			'Content-Length: '.$this->size(null),
			'Content-Disposition: attachment; filename="'.basename($filename !== null ? $filename : $this->filename()) . '"'
		);
	}
	
	/**
	 * Checks if this file exists or not. If the file does not exist a
	 * {@link FileNotFoundException} is thrown
	 * @return boolean
	 */
	public function checkExistence() 
	{
		if (empty($this->nodeName) || !$this->exists()) {
			throw new FileNotFoundException($this);
		}
		return true;
	}
	
	/**
	 * Moves/renames the file to $filename an other location
	 *
	 * Move a file into an new directory
	 * <code>
	 * // just plain
	 * $file->move('newDir/filename.jpg');
	 * // with directory instance:
	 * $file->move(new Dir('../img/userimages/', $User->id.'.'.$file->extension()));
	 * </code>
	 * @param string|Directory $newName
	 * @param string
	 * @return File
	 */
	public function move($filename, $newFilename = null, $overwrite = false) 
	{
		$this->checkExistence();
		// directory class passed as param
		if ($filename instanceof Dir) {
			$dir = $filename;
			$filename = $dir->nodeName.$newFilename;
		} else {
			$dir = new Dir(dirname($filename));
		}
		// check directory existence
		$dir->checkExistence();
		if (file_exists($filename)) {
			if ($overwrite) {
				unlink($filename);
			}
		}
		if ($this->isUploaded()) {
			move_uploaded_file($this->nodeName, $filename);	
		} else {
			rename($this->nodeName, $filename);
		}
		$className = get_class($this);
		return new $className($filename);
	}
	
	/**
	 * Renames the file to the new $filename passed, alias for {@link move}
	 * @param string $filename
	 * @return File
	 */
	public function rename($filename, $newFilename = null) 
	{
		return $this->move($filename, $newFilename);
	}
	
	/**
	 * Copies this file to an other location and returns a File Instance of
	 * the new file if the copy action succeeds
	 * @param string $newName
	 * @return File
	 */
	public function copy($newName) 
	{
		$this->checkExistence();
		// test if aiming directory is existent
		$dir = new Dir(dirname($newName));
		$dir->checkExistence();
		copy($this->nodeName, $newName);
		$className = get_class($this);
		return new $className($newName);
	}
	
	/**
	 * Alias method for {@link copy}
	 * @param $newName
	 * @return File
	 */
	public function copyTo($newName) 
	{
		return $this->copy($newName);
	}
	
	/**
	 * Returns the file extension of the File if there's any
	 * @param string $new new extension to use (renames the file)
	 * @return string
	 */
	public function extension($new = null) 
	{
		// change file extension
		if ($new !== null) {
			return $this->rename($this->basename(false).'.'.$new);
		}
		return File::ext($this->nodeName);
	}
	
	/**
	 * Returns the file extentions from a $filename.
	 * 
	 * Empty extensions will result in a false as return value.
	 * 
	 * This method is multi-byte save beacuse the file/extension devider is a
	 * single byte character.
	 * 
	 * @param string $filename
	 * @return string|boolean false if no extension is found
	 * @static
	 */
	public static function ext($filename, $noCase = true) {
		if (($pos = strrpos($filename, '.')) === false) {
			return false;
		} else {
			if ($noCase) {
				return strtolower(trim(substr($filename, $pos + 1)));
			} else {
				return trim(substr($filename, $pos + 1));
			}
		}
	}
	
	/**
	 * Returns the mime type of this file if found in {@link MimeTypes}
	 * @return string
	 */
	public function mimeType() 
	{
		// try to guess mime type on image type
		$imgInfo = @getimagesize($this->nodeName);
		$mimeTypes = array(1 => 'image/gif', 2 => 'image/jpg', 3 => 'image/png', 4 => 'image/swf');
		if (isset($imgInfo[2]) && isset($mimeTypes[$imgInfo[2]])) {
			return $mimeTypes[$imgInfo[2]];
		}
		// try to guess mime type on file extension
		if (!($extension = $this->extension())) {
			return false;
		}
		Library::load('ephFrame.lib.util.MimeTypes');
		return MimeTypes::mimeType($this->nodeName);
	}
	
	/**
	 * Tests if this file has any extension (marked by the last .)
	 * @return boolean
	 */
	public function hasExtension() 
	{
		return strrpos(basename($this->nodeName), '.');
	}
	
	/**
	 * Returns the filesize in Bytes or a string of the filesize as in human
	 * readable format using {@link sizeHumanized}
	 * @param integer $precision
	 * @param boolean $humanized
	 * @return integer|string
	 */
	public function size($precision = 2, $humanized = false) 
	{
		if (!$this->exists()) throw new FileNotFoundException($this);
		if ($humanized) {
			return self::sizeHumanized(filesize($this->nodeName), $precision);
		} else {
			return filesize($this->nodeName);
		}
	}
	
	/**
	 * Alias for {@link size}
	 * @param integer $precision
	 * @param boolean $humanized
	 * @return integer|string
	 */
	public function filesize() 
	{
		return $this->size($precision = 2, $humanized = false);
	}
	
	/**
	 * Returns the filesize in a human readable format
	 * @param integer $size
	 * @param integer $precision
	 * @return string
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
	 * Tries to translate 32M or 34KB or even 2324 TB into the equivalent
	 * byte size and returns it.
	 * @param string $size
	 * @return integer
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
	 * Write $string to the end of the current file
	 * @param string $string
	 * @return File
	 */
	public function append($string) 
	{
		if (!$this->exists()) $this->create();
		if (!$this->readable()) throw new FileNotFoundException($this);
		file_put_contents($this->nodeName, $string, FILE_APPEND);
		return $this;
	}
	
	/**
	 * Reads a single line from a file with optional skipping empty lines
	 * @param boolean $notEmpty
	 * @return string
	 */
	public function read() 
	{
		if (!is_resource($this->stream)) {
			$this->checkExistence();
			$this->stream = fopen($this->nodeName, 'r');
		}
		if (feof($this->stream)) {
			$this->close();
			return false;
		}
		$result = fgets($this->stream, $this->readBufferSize);
		return $result;
	}
	
	/**
	 * Returns the hole content of a file as string
	 * @return string
	 */
	public function slurp() 
	{
		$this->checkExistence();
		return file_get_contents($this->nodeName);
	}
	
	/**
	 * Reads the hole file into an array and returns the array
	 * @return array(string)
	 */
	public function toArray() 
	{
		$this->checkExistence();
		if (!$this->readable()) throw new FileNotReadableException($this);
		return array_map('trim', file($this->filename()));
	}
	
	/**
	 * Write to a file
	 * 
	 * Write $content to a file. If the file does not exists the class will
	 * try to create it.
	 * 
	 * @param string
	 * @return File
	 */
	public function write($content) 
	{
		if (!$this->exists()) {
			$fp = $this->create();
		}
		$fp = fopen($this->nodeName, 'w');
		fputs($fp, $content, strlen($content));
		fclose($fp);
		return $this;
	}
	
	/**
	 * Creates a file with the given name or re-creates the current file
	 * @param string $filename filename of a new file to create
	 * @param integer $chmod chmod of the new file
	 * @return boolean success
	 */
	public function create($filename = null, $chmod = 0644) 
	{
		if ($filename === null) {
			$fp = @fopen($this->nodeName, 'w');	
		} else {
			$fp = @fopen($this->nodeName, 'w');
		}
		if ($filename == null) {
			$file = $this;
		} else {
			$classname = get_class($this);
			$file = new $classname($filename);
		}
		if (!$fp) {
			throw new FileNotWriteableException($file);
		}
		$file->chmod($chmod);
		return $file;
	}
	
	/**
	 * Saves the file as $filename and returns a new File Instance of the 
	 * new created file.
	 * @param string $filename
	 * @return File
	 */
	public function saveAs($filename) 
	{
		if (!empty($this->nodeName)) {
			$this->checkExistence();
		}
		$classname = get_class($this);
		$newFile = new $classname($filename);
		$newFile->write(file_get_contents($this->nodeName));
		return $newFile;
	}
	
	/**
	 * Return MD5 Sum of file contents
	 * @return string
	 */
	public function MD5()
	{
		$this->checkExistence();
		return md5_file($this->filename());
	}
	
	/**
	 * Checks a the file for possible php tags, this is used
	 * by some site attacks, that upload images with php code
	 * @throws FileWithPHPTagsException
	 */
	public function checkForPHPTags() 
	{
		$this->checkExistence();
		if (!$this->readable()) throw new FileNotReadableException($this);
		while($line == $this->read()) {
			$line = fgets($fp, $this->readBufferSize);
			if (preg_match('', $line)) throw new FileWithPHPTagsException($this);
		}
		$this->close();
		return $this;
	}
	
	public function close() 
	{
		if (is_resource($this->stream)) {
			fclose($this->stream);
		}
		return true;
	}
	
	public function __destroy() 
	{
		$this->close();
	}
}

/**
 * Basic File Exception
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileException extends FileSystemNodeException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileNotFoundException extends FileException 
{
	public function __construct(File $file) 
	{
		parent::__construct('Unable to find file \''.$file->nodeName.'\'.');
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileNotReadableException extends FileException 
{
	public function __construct(File $file) 
	{
		parent::__construct('Unable to read from file \''.$file->nodeName.'\'.');
	}
}

/**
 * Thrown if a file is not writable or can not be created
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileNotWriteableException extends FileException 
{
	public function __construct(File $file) 
	{
		parent::__construct('Unable to write file \''.$file->nodeName.'\'.');
	}
}