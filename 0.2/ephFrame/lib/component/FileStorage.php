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
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

// load classes used by this class
class_exists('Dir') or require dirname(__FILE__).'/../Dir.php';
class_exists('File') or require dirname(__FILE__).'/../File.php';
class_exists('String') or require dirname(__FILE__).'/../helper/String.php';

/**
 * Stores and returns files
 * 
 * The file storage is some kind of man in the middle between you saving
 * files and the directory/file structure. Imagine you project to save more
 * than 100k user images in one folder - which you might have allready done.
 * This might be not very effective and performant on -nix systems. File lookups
 * and terminal commands get very slow with a ton of files in directory.
 * So that's the story why i wrote this class.<br />
 * <br />
 * This class donates you the ability by saving the files just like you did
 * in the past in subfolders without caring about the subfolder structure.<br />
 * <br />
 * Let's see this small example:
 * <code>
 * move_uploaded_file($_FILES['userpic']['tmp_name'], 'img/users/avatar_'.$userId.'.jpg');
 * </code>
 * This can be extrodinary big! Imagine you have 100k+ users! That would be 
 * 100k+ files. On -Nix Systems a directory listing would really take a lot of
 * time wich is a pain in the ass when you just want to check something.<br />
 * <br />
 * So here's the cooler way!
 * <code>
 * $fileStorage->method = FileStorageComponent::METHOD_NUMERIC;
 * $fileStorage->store($_FILES['userpic']['tmp_name'], 'img/users/avatar_'.$userId.'.jpg');
 * </code>
 * That will store the file in a subfolder of img/users/ depending on the file
 * name. In this example (userid is 1231239 f.e) in /1/2/3/1/2/3/9/avatar_1231239.jpg.<br />
 * <br />
 * So here you are. The almost same shit, but a better file distribution!<br />
 * <br />
 * <strong>Callbacks:</strong><br />
 * Year! There some! {@link beforeStore} is one - check this out in your sub-classes
 * for creating checks on the file, before saving them! (imaginable image
 * resolution or file type checks)<br />
 * <br />
 * <strong>Storage Methods:</strong><br />
 * This class supports some different storage methods that increase or decrease
 * the distribution (file per folder) value. Some calculations:<br />
 * <ul>
 * 	<li>METHOD_PLAIN<br />
 * 	the files are stored after their filename. you store abc.jpg, is stored
 * 	in a/b/c/abc.jpg, without any further manipulation. This one is the easiest
 * 	if you want to find your file without this class.
 * 	</li>
 * 	<li>METHOD_NUMERIC<br />
 * 	the files are stored after each number found in the filename. You want
 * 	to store a file named userprofile_12124.jpg it's stored in 
 * 	1/2/1/2/4/121224.jpg.
 * 	</li>
 * 	<li>METHOD_MD5 (recommended):<br />
 * 	the filename is converted to md5 value (16 possible chars) before
 * 	storing.</li>	
 * </ul>
 * 
 * @todo delete directories that are empty after removing something from the storage
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 26.11.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @uses File
 * @uses Dir
 * @uses String
 */
class FileStorageComponent extends AppComponent {
	
	/**
	 * Root directory the store stores the files
	 * @var string
	 */
	public $root = '';
	
	/**
	 * Defines the maximum level of distribution depth
	 * depending on the method you use for the distribution, the distribution
	 * depth defines the maximum files that can be stored.
	 * Set to 0 for maximum depth (depends on the os)
	 * @var integer
	 */
	public $maxDepth = 4;
	
	const METHOD_PLAIN = 0;
	const METHOD_MD5 = 1;
	const METHOD_NUMERIC = 2;
	
	/**
	 * Sets the way the storage creates sub folders for the files it's storing
	 * Use the constants from above for setting
	 * @var integer
	 */
	public $method = self::METHOD_MD5;
	
	/**
	 * MD5 Salt for security reasons, used when $method is set to METHOD_MD5
	 * You should modify this if you want to use it.
	 * @var string
	 */
	public $md5Salt = '';
	
	/**
	 * Returns a {@link FileComponent} instance of a file in the storage
	 * @param string $storageName
	 * @return FileComponent
	 */
	public function get($storageName) {
		return new File($this->translate($storageName));
	}
	
	/**
	 * Checks the storage for the existance of file with the given $storageName
	 * @param string $storageName
	 * @return boolean
	 */
	public function isStored($storageName) {
		$internalFileName = $this->root.$this->translate($storageName);
		if (file_exists($internalFileName) && is_file($internalFileName)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * alias for {@link isStored}
	 * @param string $storeName
	 * @return boolean
	 */
	public function exists($storageName) {
		return file_exists($this->root.$this->translate($storageName));
	}
	
	
	/**
	 * Puts a file into the storage and returns the internal (translated)
	 * path to the file.
	 * 
	 * @param string $filename path to the file that should be put into the file storage
	 * @param string $newname new name for the file in the storage
	 * @param boolean $copy copy the file instead of moving
	 * @return string new filename in the storage
	 */
	public function store($filename, $storageName, $overwrite = false, $copy = false) {
		assert(is_string($filename) && is_string($storageName));
		if (!$this->beforeStore($filename)) {
			return false;
		}
		$root = new Dir($this->root);
		if (!$root->exists()) {
			throw new FileStorageComponentRootNotFoundException($root);
		}
		$internalFileName = $this->translate($storageName);
		$root->create(dirname($internalFileName));
		$file = new File($filename);
		$internalFile = new File($this->root.$internalFileName);
		if ($internalFile->exists()) {
			if ($overwrite === true) {
				$internalFile->delete();
			} else {
				throw new FileStorageComponentFileAllreadyStoredException($storageName);
			}
		}
		$fullPathNewFile = $root->nodeName.$internalFileName;
		if ($copy === true) {
			$file->copy($fullPathNewFile);
		} else {
			// file move test if uploaded file, so we don#t need that here
			$file->move($fullPathNewFile);
		}
		return $internalFileName;
	}
	
	/**
	 * before storage callback, implement your own file is valid logic here
	 * @param string $filename
	 * @return boolean
	 */
	public function beforeStore($filename) {
		return true;
	}
	
	/**
	 * Translates a filename from external base to file storage internal base
	 * @param string $filename
	 * @return string
	 */
	public function translate($externalFilename) {
		// preparations
		if (substr($this->root, -1, 1) != DS) {
			$this->root .= DS;
		}
		// check root directory
		$root = new Directory($this->root);
		// split up the external filename
		if ($extension = File::ext($externalFilename)) {
			$basename = substr(basename($externalFilename), 0, -strlen($extension)-1);
		} else {
			$basename = basename($externalFilename);
		}
		$folder = dirname($externalFilename);
		if ($folder == '.') {
			$folder = '';
		}
		if (!empty($folder)) {
			$folder .= DS;
		}
		$distributionIndicator = $this->extractDistributionIndicator($basename);
		if ($this->maxDepth) {
			$folder .= strtolower(StringComponent::salt(substr($distributionIndicator, 0, $this->maxDepth), DS));
		} else {
			$folder .= strtolower(StringComponent::salt(substr($distributionIndicator, 0, 32), DS));
		}
		if (empty($extension)) {
			$translated = $folder.strtolower(basename($basename));
		} else {
			$translated = $folder.strtolower($basename.'.'.$extension);
		}
		return $translated;
	}
	
	/**
	 * @param string $basename
	 * @return string
	 */
	private function extractDistributionIndicator($basename) {
		if (empty($basename)) {
			throw new FileStorageComponentTranslationException();
		}
		switch ((int) $this->method) {
			default:
			case self::METHOD_PLAIN:
				return $basename;
				break;
			case self::METHOD_NUMERIC:
				$basename = preg_replace('/[^0-9]/', '', $basename);
				if (empty($basename)) {
					throw new FileStorageComponentTranslationException();
				}
				return $basename;
				break;
			case self::METHOD_MD5:
				if (empty($this->md5Salt)) {
					return md5($basename);
				} else {
					return md5(String::salt($basename, $this->md5Salt));
				}
				break;
		}
	}
	
	/**
	 * Removes a file from the storage
	 * @param string
	 * @return boolean
	 */
	public function delete($storeName) {
		@unlink($this->root.$this->translate($storeName));
	}
	
	/**
	 * Alias for {@link delete}
	 * @param string
	 * @return boolean
	 */
	public function remove($filename) {
		return $this->delete($filename);
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileStorageComponentException extends ComponentException {}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileStorageComponentRootNotFoundException extends FileStorageComponentException {
	public function __construct(Directory $root) {
		parent::__construct('Unable to find root of file storage component \''.$root->nodeName.'\'');
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileStorageComponentFileAllreadyStoredException extends FileStorageComponentException {
	public function __construct($filepath) {
		parent::__construct('There\'s allready a file stored with this name: \''.$filepath.'\'.');
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FileStorageComponentTranslationException extends FileStorageComponentException {}