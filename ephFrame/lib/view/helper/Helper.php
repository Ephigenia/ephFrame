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

class_exists('Object') or require dirname(__FILE__).'/../Object.php';

/**
 * Abstract Helper Class
 * 
 * A Helper is sometimes a singleton class or simpler classes.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 19.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 */
abstract class Helper extends Object
{	
	protected $controller;
	
	public $helpers = array();

	public function __construct($controller = null) 
	{
		if (is_object($controller))	$this->controller = $controller;
		$this->__mergeParentProperty('helpers');
		$this->init();
		return $this;
	}
	
	public function init() 
	{
		$this->initHelpers();
		return true;
	}
	
	public function startup() 
	{
		return true;
	}
	
	protected function initHelpers()
	{
		foreach($this->helpers as $HelperName) {
			$className = ClassPath::className($HelperName);
			if (!class_exists($className)) {
				loadHelper($HelperName);
			}
			$this->{$className} = new $className();
		}
		return true;
	}
	
	public function beforeAction() 
	{
		return true;
	}
	
	public function afterAction() 
	{
		return true;
	}
	
	public function beforeRender() 
	{
		return true;
	}
	
	public function afterRender($content = null) 
	{
		return $content;
	}
}