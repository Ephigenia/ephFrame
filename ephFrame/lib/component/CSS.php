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

class_exists('File') or require dirname(__FILE__).'/../file/File.php';
class_exists('Collection') or require dirname(__FILE__).'/../util/Collection.php';
class_exists('String') or require dirname(__FILE__).'/../util/String.php';

/**
 * Class Collecting CSS definitions and files for the view
 * 
 * The directory used for rendering the css file includes is STATIC_DIR by
 * default. You can set your own css directory changing dir property of CSS.<br />
 * <br />
 * The simplest example of how you can use this component is by showing you
 * how ot add css files or css rules to your application:
 * <code>
 * class ExampleController extends AppController {
 * 	public $components = array('CSS');
 * 	public function exampleAction() {
 * 		$this->CSS->link('css/main.css');
 * 	}
 * }
 * </code>
 * 
 * The cool thing about the ephFrame is that you can add css files even from
 * an {@link Element} which are in your views! So here a simple example to add a
 * CSS File from an element, like for example <q>/view/elements/menu.php</q>
 * <code>
 * 	// add meun styles for the element
 * 	$CSS->addFile('css/menu.css');
 * </code>
 * 
 * The effort about it that you get collected code. Elements in the view collect
 * js, css code. No peated code. So you might like it - DRY Style ;-) Ho!
 * 
 * @todo add external css scripts, like http://lalaland.de/fancy.css
 * @author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * @since 11.05.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @uses CSSCompressor
 * @uses CSSPacker
 * @uses Collection
 */
class CSS extends AppComponent
{	
	/**
	 * Collection that stores the name of css files added
	 * @var Collection
	 */
	public $files = array();
	
	/**
	 * Collection that stores css files that are added by url
	 * @var Collection
	 */
	public $urls = array();
	
	/**
	 * Store plain text css definitions
	 * @var array(string)
	 */
	public $plain = array();
	
	/**
	 * Will compress external files as well
	 * @var boolean
	 */
	public $compress = true;
	
	/**
	 * Turns automatic css file packaging on
	 * @var boolean
	 */
	public $pack = true;
	
	/**
	 * Directories where css files can exist, add multiple paths
	 * that CSS should search in
	 * @var string
	 */
	public $dirs = array(
		'static/css/',
	);
	
	public function clear() 
	{
		$this->files = new Collection();
		$this->urls = new Collection();
		$this->plain = array();
		return $this;
	}
	
	public function startup() 
	{
		$this->clear();
		return parent::startup();
	}
	
	/**
	 * Add plain css definitions
	 * <code>
	 * $CSS->add('body: { font-family: Arial; }');
	 * </code>
	 * @param string $css
	 * @return CSS
	 */
	public function add($css) 
	{
		if (!in_array($css, $this->plain)) {
			$this->plain[] = $css;
		}
		return $this;
	}
	
	/**
	 * Add a single or multiple files to javascript
	 * 
	 * <code>
	 * $JavaScript->addFile('test.js');
	 * $JavaScript->addFile(array('test.js'));
	 * </code>
	 * 
	 * @param string $filename
	 * @return JavaScript
	 */
	public function addFile($filename) 
	{
		$args = func_get_args();
		if (is_array($filename)) {
			$args = $filename;
		}
		array_map('strval', array_map('trim', $args));
		foreach($args as $filename) {
			if (Validator::URL($filename)) {
				$this->urls->add($filename);
			} else {
				if (strpos($filename, '?') === false) {
					$filename = String::append($filename, '.css', true);
				}
				$this->files->add($filename);
			}
		}
		return $this;
	}
	
	/**
	 * Alias for {@link link}
	 * @param $files
	 * @return JavaScript
	 */
	public function addFiles($files) 
	{
		$args = func_get_args();
		return $this->callMethod('addFile', $args);
	}
	
	/**
	 * Alias for {@link link}
	 * @param $files
	 * @return JavaScript
	 */
	public function link($filename) 
	{
		$args = func_get_args();
		return $this->callMethod('addFile', $args);
	}
	
	public function render() 
	{
		if (!$this->beforeRender()) return false;
		$rendered = '';
		// render include tags for css files
		foreach(array_merge($this->files->toArray(), $this->urls->toArray()) as $filename) {
			if (substr($filename, 0, 7) !== 'http://') {
				$filename = WEBROOT.$filename;
			}
			$tag = new HTMLTag('link', array(
				'rel' => 'stylesheet', 'type' => 'text/css',
				'href' => $filename
			));
			$rendered .= $tag->render().LF;
		}
		// render plain css definitions
		if (count($this->plain) > 0) {
			$styleTag = new HTMLTag('style', array('type' => 'text/css'));
			if ($this->compress) {
				Library::load('ephFrame.lib.component.CSSCompressor');
				$CSSCompressor = new CSSCompressor();
				$styleTag->tagValue($CSSCompressor->compress(implode(LF, $this->plain)));
			} else {
				$styleTag->tagValue(implode(LF, $this->plain));
			}
			$rendered .= $styleTag->render();
		}
		return $this->afterRender($rendered);
	}
	
	public function beforeRender(Controller $controller = null) 
	{
		if ($controller instanceof Controller) return parent::beforeRender($controller);
		// add themed dir if theme is set in controller
		if (!empty($this->controller->theme)) {
			array_unshift($this->dirs, 'static/theme/'.$this->controller->theme.'/css/');
		}
		// filter files that don't exist
		foreach($this->files as $filename) {
			foreach($this->dirs as $dirname) {
				if (!file_exists($dirname.$filename)) continue;
				$existingFiles[] = $dirname.$filename;
				break 1;
			}
		}
		$this->files = new Collection(@$existingFiles);
		// pack files, if {@link pack} is on and everything is smooothy
		if ($this->pack) {
			Library::load('ephFrame.lib.component.CSSPacker');
			$packer = new CSSPacker();
			$compressedFilename = $this->dirs[0].$packer->packAndStore($this->files->toArray(), $this->dirs[0]);
			$this->files = new Collection($compressedFilename);
		}
		return parent::beforeRender($controller);
	}
	
	public function __toString()
	{
		return $this->render();
	}
}