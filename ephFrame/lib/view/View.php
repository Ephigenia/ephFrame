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
 * View (part of MVC)
 * 
 * A view is something the user gets to see when he uses the app.
 * 
 * The View is generated from a layout file base and rendering a view created
 * from the controller name and action. This is a vary deep part of the ephFrame
 * framework.
 * 
 * A View can render {@link Element}s by using this kind of code in a view template.
 * Read more about elements in the docs of {@link Element}
 * <code>
 * // element must be located in /app/views/elements/elementName.php
 * echo $this->renderElement('elementName', array('dataVarName' => 'value');
 * // element must be located in /app/views/elements/sub/elementName.php
 * echo $this->renderElement('sub/elementName', array('dataVarName' => 'value');
 * </code>
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 09.08.2007
 * @version 0.2
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 */
class View
{	
	/**
	 * Name of this view (this is used to get the
	 * Directory name)
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $name = 'index';
	
	/**
	 * Optional name for a theme to use in the path
	 * @var string
	 */
	public $theme = false;
	
	/**
	 * Extension for layout and view files
	 * this is some kind of security issue here not to use html
	 * @var string
	 */
	protected $extension = 'php';
	
	/**
	 * View constructor
	 * @return View
	 */
	public function __construct($path, $name = 'index', $data = null) 
	{
		$this->path = $path;
		$this->name = $name;
		if (is_object($data)) {
			$this->data = $data;
		} else {
			$this->data = new Hash($data);
		}
		return $this;
	}
	
	protected function templateFileBasename()
	{
		return lcfirst($this->path).DS.preg_replace('@\.php$@', '', lcfirst($this->name)).'.'.$this->extension;
	}
	
	protected function templateSearchPaths()
	{
		if ($this->theme) {
			$searchPaths[] = VIEW_THEME_DIR.$this->theme.DS;
		}
		$searchPaths[] = VIEW_DIR;
		$searchPaths[] = FRAME_ROOT.'view'.DS;
		return $searchPaths;
	}
	
	protected function templateFilename()
	{
		if ($this->theme && !is_readable(VIEW_THEME_DIR.$this->theme)) {
			throw new ThemeNotFoundException($this, $this->theme);
		}
		$templateFileBasename = $this->templateFileBasename();
		foreach($this->templateSearchPaths() as $searchPath) {
			$templateFilename = $searchPath.$templateFileBasename;
			if (file_exists($templateFilename)) {
				if (!is_readable($templateFilename)) {
					throw new ViewFileNotReadableException($this, $templateFilename);
				}
				return $templateFilename;
			} else {
				$notFound[] = $templateFilename;
			}
		}
		if (preg_match('@/element/@', $templateFilename)) {
			throw new ElementFileNotFoundException($this, $notFound[0]);
		} elseif (preg_match('@/layout/@', $templateFilename)) {
			throw new LayoutFileNotFoundException($this, $notFound[0]);
		} else {
			throw new ViewFileNotFoundException($this, $notFound[0]);;
		}
	}
	
	protected function beforeRender()
	{
		return true;
	}
	
	/**
	 * Renders the view by requiring a php file based on the view action name
	 * 
	 * @throws ViewFileNotFoundException
	 * @throws LayoutFileNotFoundException
	 * @return string
	 */
	public function render()
	{
		if (!$this->beforeRender()) return false;
		foreach($this->data->toArray() as $___key => $___val) {
			${$___key} = $___val;
		}
		ob_start();
		require $this->templateFilename();
		return $this->afterRender(ob_get_clean());
	}
	
	protected function afterRender($content)
	{
		return $content;
	}
	
	/**
	 * Echoes or returns the content of an {@link Element}.
	 * 
	 * Render a page element in a view. This is usually called within a View.
	 * It accepts the name of the element or the path to it and some variables
	 * as associated array.
	 * Variables from the current view are overwritten by the elements data
	 * array!
	 * 
	 * Code from a view:
	 * <code>
	 * $this->element('mainMenu', array('menuEntries' => array(
	 * 	'main' => '/',
	 * 	'users' => '/users/'
	 * ));
	 * </code>
	 * 
	 * @param string $elementName
	 * @param array $data array of variables available in the element
	 * @return string
	 */
	public function element($name, Array $data = array()) 
	{
		// load Element class
		class_exists('Element') or require dirname(__FILE__).'/Element.php';
		// merge view data with element’s data
		if ($this->data instanceof Hash) {
			$data = array_merge($this->data->toArray(), $data);
		} elseif (is_array($this->data)) {
			$data = array_merge($this->data, $data);
		}
		// create element
		$element = new Element($name, $data);
		$element->theme = $this->theme;
		// render element but ignore not found messages
		try {
			$content = $element->render();
		} catch (ElementNotFoundException $e) { }
		return $content;
	}
	
	/**
	 * Alias for {@link render}
	 * @deprecated 2009-11-29
	 * @deprecated revision: 0.2.232
	 */
	public function renderElement($elementName, $data = array(), $output = true)
	{
		return $this->element($elementName, $data, $output);
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class ViewException extends BasicException 
{
	public $view;
	public function __construct(View $view, $message = null)
	{
		$this->view = $view;
		parent::__construct($message);
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class ViewFileNotFoundException extends ViewException
{
	public $filename;
	public function __construct(View $view, $filename)
	{
		$this->filename = $filename;
		parent::__construct($view, sprintf('Unable to locate view file \'%s\'.', $this->filename));
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class ViewFileNotReadableException extends ViewException
{
	public $filename;
	public function __construct(View $view, View $filename)
	{
		$this->filename = $filename;
		parent::__construct($view, sprintf('Unable to read view file: \'%s\'.', $this->filename));
	}
}

/** 
 * Thrown if a layout directory was not found
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class ThemeNotFoundException extends ViewException
{
	public $theme;
	public function __construct(View $view, $theme)
	{
		$this->theme = $theme;
		parent::__construct($view, sprintf('Theme \'%s\' not found.', $this->theme));
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class LayoutFileNotFoundException extends ViewException
{
	public $filename;
	public function __construct(View $view, $filename)
	{
		$this->filename = $filename;
		parent::__construct($view, sprintf('The layout template file was not found at \'%s\'', $this->filename));
	}
}