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
 * 	The Javascript component used for {@link HTMLView} class
 * 
 * 	This class can be used as a component in a controller and the views to add
 * 	js-code for jquery-document-ready, plain javascript and even js-files.<br />
 * 	The cool thing about this component is that it can be used in view {@link Element},
 * 	so you don’t have to devide the elements js code from the element anymore
 * 	they are both in the same file.
 * 
 * 	<code>
 * 	// add a simple hello world to the javascript
 * 	$JavaScript->addScript('alert("Hello World!")');
 * 	// add a hello world to jQuery Document Ready
 * 	$JavaScript->jQuery('alert("Hello World!")');
 * 	// add js file
 * 	$JavaScript->link('jquery.js');
 * 	</code>
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de
 * 	@since 16.03.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component
 * 	@uses JSCompressor
 * 	@uses JSPacker
 */
class JavaScript extends Component implements Renderable {
	
	/**
	 * 	Collection of Javascript filenames
	 * 	@var array(string)
	 */
	public $files = array();
	
	/**
	 * 	Storage for plain javascript definitions
	 * 	@var array(string)
	 */
	public $plain = array();
	public $jQuery = array();
	public $dir = './';
	
	public $compress = false;
	public $pack = false;
	
	public function startup() {
		$this->dir = STATIC_DIR.'js'.DS;
		$this->controller->set('JavaScript', $this);
		return parent::startup();	
	}
	
	public function addScript($script) {
		if (!in_array($script, $this->plain)) {
			$this->plain[] = $script;
		}
		return $this;
	}
	
	public function jQuery($script) {
		if (!in_array($script, $this->jQuery)) {
			$this->jQuery[] = $script;
		}
		return $this;
	}
	
	public function link($filename) {
		return $this->addFile($filename);
	}

	public function addFile($filename) {
		if (func_num_args() > 1) {
			$args = func_get_args();
			return $this->addFiles($args);
		}
		$filename = basename($filename);
		if (strcasecmp(substr($filename, -3), '.js') !== 0) {
			$filename .= '.js';
		}
		if (!in_array($filename, $this->files)) {
			$this->files[] = $filename;
		}
		return $this;
	}
	
	public function addFiles($files) {
		if (func_num_args() == 1) {
			if (!is_array($files)) {
				$fiels = array($files);
			}
		} elseif (func_num_args() > 1) {
			$files = func_get_args();
		}
		foreach($files as $filename) {
			$this->addFile($filename);
		}
		return $this;
	}
	
	public function render() {
		if (!$this->beforeRender()) return '';
		$rendered = '';
		foreach($this->files as $filename) {
			$cssIncludeTag = new HTMLTag('script', array(
				'type' => 'text/javascript',
				'src' => str_replace('//','', WEBROOT.$this->dir.$filename)
			));
			$rendered .= $cssIncludeTag->render();
		}
		if (!empty($this->plain) || !empty($this->jQuery)) {
			$plain = implode(LF, $this->plain);
			$jQuery = implode(LF, $this->jQuery);
			if ($this->compress) {
				loadComponent('JSCompressor');
				$compressor = new JSCompressor();
				$plain = $compressor->compress($plainJoined);
				$jQuery = $compressor->compress($jQuery);
			}
			$jsSource = '//<![CDATA['.LF.
				$plain.
				LF.'$(document).ready(function() {'.LF.
				$jQuery.LF.
				'});'.LF.
				'//]]>';
			$jsScriptTag = new HTMLTag('script', array('type' => 'text/javascript'), $jsSource);
			$rendered .= $jsScriptTag->render();
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
			loadComponent('JSPacker');
			$packer = new JSPacker();
			$packer->compress = $this->compress;
			$compressedFilename = $packer->packAndStore($files, $dir);
			$this->files = array($compressedFilename);
		}
		return true;
	}
	
	public function afterRender($rendered) {
		return $rendered;
	}
	
}

/**
 *	@package ephFrame
 *	@subpackage ephFrame.lib.exception
 */
class JavaScriptException extends ComponentException {}


?>