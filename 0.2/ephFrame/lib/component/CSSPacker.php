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
 * 	CSS Packer, packs CSS Files into single files
 * 
 * 	A Packer packs files into one single file. That should reduce HTTP-Requests.
 * 
 * 	Pack css files and echo result:
 * 	<code>
 * 	$CSSPacker = new CSSPacker();
 * 	$CSSPacker->pack(array('core.css', 'app.css', 'menu.css');
 * 	</code>
 * 
 * 	Pack css files and store them (code is an example from a possible controller)
 * 	<code>
 * 	public $components = array('CSSPacker');
 * 	public $beforeRender() {
 * 		$packer = new CSSPacker();
 * 		echo $packer->packAndStore(array('css/main.css', 'css/grid.css'), '../css/packed/');
 *  }
 * 	</code>
 * 
 * 	@author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * 	@since 12.05.2008
 * 	@subpackage ephFrame.lib.component
 * 	@package ephFrame
 * 	@uses CSSCompressor
 * 	@uses File
 */
class CSSPacker extends Component {
	
	public $compress = true;
	
	/**
	 * 	Compressor instance used when {@compress} is true
	 * 	@var CSSCompressor
	 */
	public $compressor;
	
	/**
	 * 	Stores the name of the class that should be used to compress
	 * 	the css files if {@link compress} is true
	 * 	@return string
	 */
	public $compressorClassname = 'CSSCompressor';
	
	/**
	 * 	Extension for packed CSS Files, without point
	 * 	@var string
	 */
	public $packedExtension = 'css';
	
	/**
	 * 	FilenamePrefix for compressed filenames, no slashes
	 * 	@var string
	 */
	public $packedPrefix = 'p_';
	
	/**
	 * 	@return CSSPacker
	 */
	public function __construct() {
		ephFrame::loadClass('ephFrame.lib.File');
		loadComponent($this->compressorClassname);
		$this->compressor = new $this->compressorClassname();
		return $this;
	}
	
	/**
	 * 	Compresses multiple css files and stores them as a new file in the
	 * 	target dir. The return value is the new filename of the packaged css
	 * 	files.
	 *
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
	 * 	Extracts (if found) the charset @-directive of a css string content
	 * 	@param string $cssContent
	 * 	@return boolean|array(string) false if nothing found, otherwise a string with the charset encoding name found
	 */
	private function captureCharset($cssString) {
		if (preg_match('/^[\s\n\r]*@charset\s+["\'](.+)["\'];/i', $cssString, $found)) {
			return $found;
		}
		return false;
	}
	
	/**
	 * 	Compresses multiple CSS files and returns the compressed content.
	 * 	Till now the @-Tags @media, @import & @page are ignored in the
	 * 	compressed content maybe.
	 *
	 * 	@param array $files Array of absolute paths to css files that should be packed
	 * 	@return string
	 */
	public function pack(Array $files) {
		$packed = '';
		foreach($files as $filename) {
			// only compress if turned on
			if ($this->compress) {
				$content = $this->compressor->compressFile($filename);
			} else {
				$file = new File($filename);
				$content = $file->read();
			}
			// capture encoding tag, last one wins
			if ($encoding = $this->captureCharset($content)) {
				$content = substr($content, strlen($encoding[0]));
			}
			$packed .= $content;
		}
		// add the encoding at the top of the file if there was any encoding
		if (!empty($encoding)) {
			$packed = '@CHARSET "'.$encoding[1].'";'.$packed;
		}
		return $packed;
	}
	
	/**
	 * 	Generates a unique filename using the basenames of the files in the
	 * 	array passed and returns it.
	 *
	 * 	@param array(string) $files
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
class CSSPackerException extends ComponentException {}

?>