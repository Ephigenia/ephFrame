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

class_exists('String') or require dirname(__FILE__).'/../helper/String.php';

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

	public function clear() {
		$this->files = new Collection();
		$this->plain = array();
		$this->jquery = array();
		return $this;
	}
	
	public function startup() {
		$this->clear();
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
	
	/**
	 * 	Add a single or multiple files to javascript
	 * 
	 * 	<code>
	 * 	$JavaScript->addFile('test.js');
	 * 	$JavaScript->addFile(array('test.js'));
	 * 	</code>
	 * 
	 * 	@param string $filename
	 * 	@return JavaScript
	 */
	public function addFile($filename) {
		$args = func_get_args();
		if (count($args[0]) > 1) {
			return $this->addFile($args[0]);
		}
		foreach($args as $filename) {
			$filename = trim((string) $filename);
			$filename = String::append($filename, '.js', true);
			if (substr($filename, 0, 7) != 'http://') {
				$filename = WEBROOT.$this->dir.$filename;
			}
			$this->files->add($filename);
		}
		return $this;
	}
	
	/**
	 * 	Alias for {@link link}
	 * 	@param $files
	 * 	@return JavaScript
	 */
	public function addFiles($files) {
		$args = func_get_args();
		return $this->callMethod('addFile', $args);
	}
	
	/**
	 * 	Alias for {@link link}
	 * 	@param $files
	 * 	@return JavaScript
	 */
	public function link($filename) {
		$args = func_get_args();
		return $this->callMethod('addFile', $args);
	}
	
	public function render() {
		if (!$this->beforeRender()) return '';
		$rendered = '';
		foreach($this->files as $filename) {
			$tag = new HTMLTag('script', array(
				'type' => 'text/javascript',
				'src' => $filename
			));
			$rendered .= $tag->render();
		}
		
		if (!empty($this->plain) || !empty($this->jQuery)) {
			$plain = implode(LF, $this->plain);
			$jQuery = implode(LF, $this->jQuery);
			// compress plain javascript
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
			$files = array();
			foreach($this->files as $filename) {
				if (file_exists($filename)) {
					$this->files->removeAll($filename);
					$files[] = $filename;
				}
			}
			if (count($files) > 0) {
				// do the packing stuff
				loadComponent('JSPacker');
				$packer = new JSPacker();
				$packer->compress = $this->compress;
				$compressedFilename = WEBROOT.$this->dir.$packer->packAndStore($files, $this->dir);
				$this->files = array($compressedFilename);
			}
		}
		return true;
	}
		
}

/**
 *	@package ephFrame
 *	@subpackage ephFrame.lib.exception
 */
class JavaScriptException extends ComponentException {}


?>