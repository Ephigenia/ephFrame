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

class_exists('File') or require dirname(__FILE__).'/../File.php';

/**
 * CSS Packer, packs CSS Files into single files
 * 
 * A Packer packs files into one single file. That reduces HTTP-Requests.
 * The Packer will not compress files from remote servers or files that can
 * not be found or read.
 * 
 * Pack css files and echo result:
 * <code>
 * $CSSPacker = new CSSPacker();
 * $CSSPacker->pack(array('core.css', 'app.css', 'menu.css');
 * </code>
 * 
 * Pack css files and store them (code is an example from a possible controller)
 * <code>
 * public $components = array('CSSPacker');
 * public $beforeRender() {
 * 	$packer = new CSSPacker();
 * 	echo $packer->packAndStore(array('css/main.css', 'css/grid.css'), '../css/packed/');
 * }
 * </code>
 * 
 * @author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * @since 12.05.2008
 * @subpackage ephFrame.lib.component
 * @package ephFrame
 * @uses CSSCompressor
 * @uses File
 */
class CSSPacker extends AppComponent {
	
	public $compress = true;
	
	/**
	 * Compressor instance used when {@compress} is true
	 * @var CSSCompressor
	 */
	public $compressor;
	
	/**
	 * Stores the name of the class that should be used to compress
	 * the css files if {@link compress} is true
	 * @return string
	 */
	public $compressorClassname = 'CSSCompressor';
	
	/**
	 * Extension for packed CSS Files, without point
	 * @var string
	 */
	public $packedExtension = 'css';
	
	/**
	 * FilenamePrefix for compressed filenames, no slashes
	 * @var string
	 */
	public $packedPrefix = 'p_';
	
	/**
	 * @return CSSPacker
	 */
	public function __construct() {
		loadComponent($this->compressorClassname);
		$this->compressor = new $this->compressorClassname();
		return $this;
	}
	
	/**
	 * Compresses multiple css files and stores them as a new file in the
	 * target dir. The return value is the new filename of the packaged css
	 * files.
	 *
	 * @param array(string) $files
	 * @param string $targetDir
	 */
	public function packAndStore(Array $files, $targetDir) {
		assert(!empty($targetDir));
		$dir = new Dir($targetDir);
		$packedFile = $dir->newFile($this->packedFilename($files), $this->pack($files));
		return $packedFile->basename();
	}
	
	/**
	 * Extracts (if found) the charset @-directive of a css string content
	 * @param string $cssContent
	 * @return boolean|array(string) false if nothing found, otherwise a string with the charset encoding name found
	 */
	private function captureCharset($cssString) {
		if (preg_match('/^[\s\n\r]*@charset\s+["\']([^"\']+)["\'];\s*/i', $cssString, $found)) {
			return $found;
		}
		return false;
	}
	
	/**
	 * Compresses multiple CSS files and returns the compressed content.
	 * Till now the @-Tags @media, @import & @page are ignored in the
	 * compressed content maybe.
	 *
	 * @param array $files Array of absolute paths to css files that should be packed
	 * @return string
	 */
	public function pack(Array $files) {
		$packed = '';
		foreach($files as $filename) {
			// only compress if turned on
			if ($this->compress) {
				$content = $this->compressor->compressFile($filename);
			} else {
				$file = new File($filename);
				$content = $file->slurp();
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
	 * Creates a filename for all packed files
	 *
	 * @param array(string) $files
	 * @return string
	 */
	public function packedFilename(Array $files = array()) {
		$md5Filenames = substr(md5(implode('', array_map('basename', $files))), 0, 8);
		$compressedFileName = $this->packedPrefix.$md5Filenames.'.'.$this->packedExtension;
		$compressedFileName = str_replace('/[^-_A-Za-z0-9\./', '', $compressedFileName);
		return $compressedFileName;
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class CSSPackerException extends ComponentException {}