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

ephFrame::loadClass('ephFrame.lib.File');
ephFrame::loadClass('ephFrame.lib.Collection');

/**
 * 	Class Collecting CSS definitions and files for the view
 * 
 * 	The directory used for rendering the css file includes is STATIC_DIR by
 * 	default. You can set your own css directory changing dir property of CSS.
 * 
 * 	Add a CSS File in a {@link View} or {@link Element}:
 * 	<code>
 * 	$CSS->addFile('css/main.css');
 * 	</code>
 * 
 * 	You also can add CSS Files in a {@link Controller}
 * 	<code>
 * 	class ExampleController extends AppController {
 * 		public $components = array('CSS');
 * 		public function exampleAction() {
 * 			$this->CSS->addFile('css/main.css');
 * 		}
 * 	}
 * 	</code>
 * 
 * 	@author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * 	@since 11.05.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component
 * 	@uses CSSCompressor
 * 	@uses CSSPacker
 * 	@uses Collection
 */
class CSS extends Component implements Renderable {
	
	/**
	 * 	Collection that stores the name of css files added
	 * 	@var Collection
	 */
	public $files = array();
	
	/**
	 * 	Store plain text css definitions
	 * 	@var array(string)
	 */
	public $plain = array();
	
	/**
	 * 	Will compress external files as well
	 * 	@var boolean
	 */
	public $compress = true;
	
	/**
	 * 	Turns automatic css file packaging on
	 * 	@var boolean
	 */
	public $pack = true;
	
	/**
	 * 	Directory where CSS Files are located
	 * 	@var string
	 */
	public $dir;
	
	public function __construct() {
		$this->files = new Collection();
		return parent::__construct();
	}
	
	public function startup() {
		$this->dir = STATIC_DIR.'css'.DS;
		$this->controller->set('CSS', $this);
		return parent::startup();
	}
	
	/**
	 * 	Add plain css definitions
	 * 	<code>
	 * 	$CSS->add('body: { font-family: Arial; }');
	 * 	</code>
	 * 	@param string $css
	 * 	@return CSS
	 */
	public function add($css) {
		$this->plain[] = $css;
		return $this;
	}
	
	/**
	 * 	Add one file to the list of css files that are used in the view. Missing
	 * 	File extensions are automaticly added.
	 * 	@param string $file
	 * 	@return CSS
	 */
	public function addFile($file) {
		if (func_num_args() > 1) {
			foreach(func_get_args() as $file) {
				$this->addFile($file);
			}
		} else {
			$file = basename($file);
			// add file extension
			if (strcasecmp(File::ext($file), 'css') !== 0) {
				$file .= '.css';
			}
			$this->files->add($file);
		}
		return $this;
	}
	
	public function addFiles($cssFiles) {
		if (func_num_args() == 1) {
			if (!is_array($cssFiles)) {
				$cssFiles = array($cssFiles);
			}
		} elseif (func_num_args() > 1) {
			$cssFiles = func_get_args();
		}
		foreach($cssFiles as $filename) {
			$this->addFile($filename);
		}
		return $this;
	}
	
	public function render() {
		if (!$this->beforeRender()) return '';
		$rendered = '';
		// render include tags for css files
		foreach($this->files as $filename) {
			$cssIncludeTag = new HTMLTag('link', array(
				'rel' => 'stylesheet', 'type' => 'text/css',
				'href' => str_replace('//', '/', WEBROOT.$this->dir.$filename)
			));
			$rendered .= $cssIncludeTag->render().LF;
		}
		// render plain css definitions
		if (count($this->plain) > 0) {
			$styleTag = new HTMLTag('style', array('type' => 'text/css'));
			if ($this->compress) {
				loadComponent('CSSCompressor');
				$CSSCompressor = new CSSCompressor();
				$styleTag->tagValue($CSSCompressor->compress(implode(LF, $this->plain)));
			} else {
				$styleTag->tagValue(implode(LF, $this->plain));
			}
			$rendered .= $styleTag->render();
		}
		return $this->afterRender($rendered);
	}
	
	public function beforeRender() {
		// pack files, if {@link pack} is on and everything is smooothy
		if ($this->pack && count($this->files) > 0) {
			$dir = $this->dir;
			if (substr($dir, 0, 1) == '/') {
				$dir = substr($dir, 1);
			}
			// prepend dir to all files
			$files = array();
			foreach($this->files as $filename) {
				$fileFullName = $dir.$filename;
				if (!is_file($fileFullName) || !is_readable($fileFullName)) {
					continue;
				}
				$files[] = $fileFullName;
			}
			// do the packing stuff
			loadComponent('CSSPacker');
			$packer = new CSSPacker();
			$compressedFilename = $packer->packAndStore($files, $dir);
			$this->files = array($compressedFilename);
		}
		return true;
	}
	
	public function afterRender($rendered) {
		return $rendered;
	}

}
?>