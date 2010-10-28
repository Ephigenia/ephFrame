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

class_exists('String') or require dirname(__FILE__).'/../helper/String.php';

/**
 * The Javascript component used for {@link HTMLView} class
 * 
 * This class can be used as a component in a controller and the views to add
 * js-code for jquery-document-ready, plain javascript and even js-files.<br />
 * The cool thing about this component is that it can be used in view {@link Element},
 * so you don’t have to devide the elements js code from the element anymore
 * they are both in the same file.
 * 
 * <code>
 * // add a simple hello world to the javascript
 * $JavaScript->addScript('alert("Hello World!")');
 * // add a hello world to jQuery Document Ready
 * $JavaScript->jQuery('alert("Hello World!")');
 * // add js file
 * $JavaScript->link('jquery.js');
 * </code>
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 16.03.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @uses JSCompressor
 * @uses JSPacker
 */
class JavaScript extends AppComponent
{
	/**
	 * Collection that stores the name of js files added
	 * @var Collection
	 */
	public $files = array();
	
	/**
	 * Collection that stores js files that are added by url
	 * @var Collection
	 */
	public $urls = array();
	
	/**
	 * Storage for plain javascript definitions
	 * @var array(string)
	 */
	public $plain = array();
	
	/**
	 * Stores plain scripts for jQuery document ready block
	 * @var unknown_type
	 */
	public $jQuery = array();
	
	/**
	 * Directories where js files can exist, add multiple paths
	 * @var string
	 */
	public $dirs = array('static/js/');

	public function clear() 
	{
		$this->files = new Collection();
		$this->urls = new Collection();
		$this->plain = array();
		$this->jquery = array();
		return $this;
	}
	
	public function startup() 
	{
		$this->clear();
		return parent::startup();	
	}
	
	public function add($script) 
	{
		return $this->addScript($script);
	}
	
	public function addScript($script) 
	{
		if (!in_array($script, $this->plain)) {
			$this->plain[] = $script;
		}
		return $this;
	}
	
	public function jQuery($script) 
	{
		if (!in_array($script, $this->jQuery)) {
			$this->jQuery[] = $script;
		}
		return $this;
	}
	
	public function jQueryPrepend($script)
	{
		array_unshift($this->jQuery, $script);
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
					$filename = String::append($filename, '.js', true);
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
		if (!$this->beforeRender()) return '';
		$rendered = '';
		foreach(array_merge($this->urls->toArray(), $this->files->toArray()) as $filename) {
			if (substr($filename, 0, 7) != 'http://') {
				$filename = WEBROOT.$filename;
			}
			$tag = new HTMLTag('script', array(
				'type' => 'text/javascript',
				'src' => $filename,
			));
			$rendered .= $tag->render().LF;
		}
		if (!empty($this->plain) || !empty($this->jQuery)) {
			$plain = implode(LF, $this->plain);
			$jQuery = implode(LF, $this->jQuery);
			$jsSource = '/* <![CDATA[ */'.LF.
				$plain.
				LF.'(function($) {'.LF.
				$jQuery.LF.
				'})(jQuery);'.LF.
				'/* ]]> */';
			$jsScriptTag = new HTMLTag('script', array('type' => 'text/javascript'), $jsSource);
			$rendered .= $jsScriptTag->render();
		}
		return $this->afterRender($rendered);
	}
	
	public function beforeRender(Controller $controller = null) 
	{
		if ($controller instanceof Controller) return true;
		// add themed dir if theme is set in controller
		if (!empty($this->controller->theme)) {
			array_unshift($this->dirs, 'static/theme/'.$this->controller->theme.'/js/');
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
		return true;
	}
	
	public function __toString()
	{
		return $this->render();
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class JavaScriptException extends ComponentException 
{}