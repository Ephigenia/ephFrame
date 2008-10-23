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

/**
 *	Class for packing Javascript Files
 *  
 * 	Packs JS Files into one file, good for reducing HTTP Requests
 * 
 * 	Pack js-files and echo result:
 * 	<code>
 * 	$JSPacker = new JSPacker();
 * 	$JSPacker->pack(array('core.js', 'jquery.js');
 * 	</code>
 * 
 * 	Pack js files and store the result in a file. This code should be placed
 * 	in a controller.
 * 	<code>
 * 	public $components = array('JSPacker');
 * 	public $beforeRender() {
 * 		$packer = new JSPacker();
 * 		echo $packer->packAndStore(array('js/core.js', 'js/jquery.js'));
 *  }
 * 	</code>
 * 
 * 	@author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * 	@since 12.05.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component
 * 	@uses JSCompressor
 * 	@uses File
 */
class JSPacker extends Component {
	
	public $compress = false;
	
	/**
	 * 	Instance of the compressor that is used, usually an instance of
	 * 	{@link compressorClassname}
	 * 	@var JSCompressor
	 */
	public $compressor;
	
	/**
	 * 	Classname to use by default for compression
	 * 	@var string
	 */
	public $compressorClassname = 'JSCompressor';
	
	/**
	 * 	Extension for packed JS Files, without points
	 * 	@var string
	 */
	public $packedExtension = 'js';
	
	/**
	 * 	FilenamePrefix for compressed filenames, no slashes
	 * 	@var string
	 */
	public $packedPrefix = 'p_';
	
	/**
	 * 	@return JSPacker
	 */
	public function __construct() {
		loadComponent($this->compressorClassname);
		$this->compressor = new $this->compressorClassname();
		return $this;
	}
	
	/**
	 * 	Compresses multiple javascript files and stores them as a new file in
	 * 	the target dir. The return value is the new filename of the packaged
	 * 	files.
	 *	@todo use the {@link File} Class for storing the content
	 * 	@param array(string) $files
	 * 	@param string $targetDir
	 */
	public function packAndStore(Array $files, $targetDir) {
		$packedFilename = $this->packedFilename($files);
		$compressed = $this->pack($files);
		file_put_contents($targetDir.$packedFilename, $compressed);
		return $packedFilename;
	}
	
	/**
	 * 	Compresses multiple Javascript files and returns the compressed content.
	 * 	@param array $files
	 * 	@return string
	 */
	public function pack(Array $files) {
		if (!$this->compress) {
			ephFrame::loadClass('ephFrame.lib.File');
		}
		$packed = '';
		foreach($files as $filename) {
			if ($this->compress) {
				$packed .= $this->compressor->compressFile($filename).LF;
			} else {
				$jsFile = new File($filename);
				$packed .= $jsFile->read();
			}
		}
		return $packed;
	}
	
	/**
	 * 	Generates a unique filename using the basenames of the files in the
	 * 	array passed.
	 *	//todo as in {@link CSSPacker} outsource this method to some other class
	 * 	@param array $files
	 * 	@return string
	 */
	public function packedFilename(Array $files) {
		$compressedFileName = '';
		if (!empty($this->packedPrefix)) {
			$compressedFileName .= str_replace('/[^-_A-Za-z0-9\./', '', $this->packedPrefix);
		}
		$compressedFileName .= md5(implode('', array_map('basename', $files)));
		$compressedFileName .= '.'.str_replace('/[^-_A-Za-z0-9\./', '', $this->packedExtension);
		return $compressedFileName;
	}
	
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class JSPackerException extends ComponentException {}

?>