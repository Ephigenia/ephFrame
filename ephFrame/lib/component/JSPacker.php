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
 * Class for packing Javascript Files
 * 
 * Packs JS Files into one file, good for reducing HTTP Requests
 * 
 * Pack js-files and echo result:
 * <code>
 * $JSPacker = new JSPacker();
 * $JSPacker->pack(array('core.js', 'jquery.js');
 * </code>
 * 
 * Pack js files and store the result in a file. This code should be placed
 * in a controller.
 * <code>
 * public $components = array('JSPacker');
 * public $beforeRender() {
 * 	$packer = new JSPacker();
 * 	echo $packer->packAndStore(array('js/core.js', 'js/jquery.js'));
 * }
 * </code>
 * 
 * @author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * @since 12.05.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @uses JSCompressor
 * @uses File
 * @todo normalize File Packer to not beeing a component and JS and CSS Packer inherit from the new create class
 */
class JSPacker extends AppComponent 
{
	/**
	 * Boolean value to enable JS Compression
	 * @var unknown_type
	 */
	public $compress = false;
	
	/**
	 * Instance of the compressor that is used, usually an instance of
	 * {@link compressorClassname}
	 * @var JSCompressor
	 */
	public $compressor;
	
	/**
	 * Classname to use by default for compression
	 * @var string
	 */
	public $compressorClassname = 'ephFrame.lib.component.JSCompressor';
	
	/**
	 * Extension for packed JS Files, without points
	 * @var string
	 */
	public $extension = 'js';
	
	/**
	 * FilenamePrefix for compressed filenames, no slashes
	 * @var string
	 */
	public $prefix = 'p_';
	
	/**
	 * @return JSPacker
	 */
	public function __construct() 
	{
		$this->compressor = Library::create($this->compressorClassname);
		return $this;
	}
	
	/**
	 * Compresses multiple javascript files and stores them as a new file in
	 * the target dir. The return value is the new filename of the packaged
	 * files.
	 * 
	 * @todo use the {@link File} Class for storing the content
	 * @param array(string) $files
	 * @param string $targetDir
	 */
	public function packAndStore(Array $files, $targetDir) 
	{
		$dir = new Dir($targetDir);
		if (!$dir->exists()) {
			$dir->create();
		}
		$packedFile = $dir->newFile($this->packedFilename($files), $this->pack($files));
		return $packedFile->basename();
	}
	
	/**
	 * Compresses multiple Javascript files and returns the compressed content.
	 * @param array $files
	 * @return string
	 */
	public function pack(Array $files) 
	{
		if (!$this->compress) {
			Library::load('ephFrame.lib.File');
		}
		$packed = '';
		foreach($files as $filename) {
			if ($this->compress) {
				$packed .= LF.$this->compressor->compressFile($filename).LF;
			} else {
				$jsFile = new File($filename);
				$packed .= $jsFile->slurp().LF;
			}
		}
		return $packed;
	}
	
	/**
	 * Generates a unique filename using the basenames of the files in the
	 * array passed.
	 * @param array $files
	 * @return string
	 */
	public function packedFilename(Array $files) 
	{
		$md5Filenames = substr(md5(implode('', array_map('basename', $files))), 0, 8);
		$compressedFileName = $this->prefix.$md5Filenames.'.'.$this->extension;
		$compressedFileName = str_replace('/[^-_A-Za-z0-9\./', '', $compressedFileName);
		return $compressedFileName;
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class JSPackerException extends ComponentException 
{}