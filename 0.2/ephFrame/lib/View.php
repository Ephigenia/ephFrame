<?php

/**
 * ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

// load required parent classes
class_exists('Hash') or require dirname(__FILE__).'/Hash.php';

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
 * @version 0.1
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @uses Hash
 * @uses Element
 */
abstract class View extends Hash implements Renderable {
	
	/**
	 * Name of this view (this is used to get the
	 * Directory name)
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	private $action;
	
	/**
	 * Extension for layout and view files
	 * this is some kind of security issue here not to use html
	 * @var string
	 */
	protected $templateExtension = 'php';
	
	/**
	 * Filename for this view, created in the __constructor
	 * @var string
	 */
	public $filename;
	
	/**
	 * Optional name for a theme to use in the path
	 * @var string
	 */
	public $theme = '';
	
	/**
	 * Content type for this view that can be send to the client
	 * @var string
	 */
	public $contentType = 'text/plain';
	
	public $dir = VIEW_DIR;
	
	/**
	 * View constructor
	 * @return View
	 */
	public function __construct($name, $action = 'index', $data = null) {
		if (is_object($data)) {
			$this->data = $data;
		} else {
			$this->data = new Hash($data);
		}
		if ($this->data->get('theme', false)) {
			$this->theme = (string) $this->data->get('theme');
		}
		// sanitize name
		$this->name = preg_replace('/([^-_\/a-z0-9]*)/i', '', preg_replace('@\.php$@i', '', $name));
		// sanitize action name
		$this->action = Sanitizer::panic($action);
		return parent::__construct();
	}
	
	/**
	 * Returns the filename of this view
	 * @return string
	 */
	protected function createViewFilename () {
		$knownPart = lcfirst($this->name).DS.lcfirst($this->action).'.'.$this->templateExtension;
		// add theme
		if (!empty($this->theme)) {
			if (!is_dir($this->dir.'theme'.DS.$this->theme)) {
				throw new ThemeNotFoundException($this);
			}
			$filenames[] = $this->dir.'theme/'.$this->theme.DS.$knownPart;
		}
		$filenames[] = $this->dir.$knownPart;
		$filenames[] = FRAME_ROOT.'view/'.$knownPart;
		foreach($filenames as $this->filename) {
			if (file_exists($this->filename)) { $found = true; break; }
		}
		// view file not found
		if (empty($found)) {
			$this->filename = $filenames[0];
			if ($this->name == 'layout') {
				throw new LayoutFileNotFoundException($this);
			} else {
				throw new ViewFileNotFoundException($this);
			}
		}
		return $this->filename;
	}
	
	/**
	 * Renders the view by requiring a php file based on the view action name
	 * 
	 * @throws ViewFileNotFoundException
	 * @throws LayoutFileNotFoundException
	 * @return string
	 */
	public function render() {
		// viewfilename
		$this->createViewFilename();
		if (!$this->beforeRender()) return null;
		ob_start();
		foreach($this->data->toArray() as $___key => $___val) {
			${$___key} = $___val;
		}
		// prevent key and val from manipulation
		unset($___key);
		unset($___val);
		require $this->filename;
		$content = ob_get_clean();
		return $this->afterRender($content);
	}
	
	/**
	 * Echoes or returns the content of an {@link Element}.
	 * 
	 * This will try to render an element with the $elementName with the given	
	 * $data (data from the current controller is added automatically).
	 * If you want to disable the direct output of the element, pass $output
	 * as false.
	 * 
	 * Code from a view:
	 * <code>
	 * $this->renderElement('mainMenu', array('menuEntries' => array(
	 * 	'main' => '/',
	 * 	'users' => '/users/'
	 * ));
	 * </code>
	 * 
	 * @param string $elementName
	 * @param array $data
	 * @param boolean $output
	 * @return string
	 */
	public function renderElement($elementName, $data = array(), $output = true) {
		// load Element class
		class_exists('Element') or require dirname(__FILE__).'/Element.php';
		// merge view data with element’s data
		if ($this->data instanceof Hash) {
			$data = array_merge($this->data->toArray(), $data);
		} elseif (is_array($this->data)) {
			$data = array_merge($this->data, $data);
		}
		// create element
		$element = new Element($elementName, $data);
		$element->theme = $this->theme;
		if ($output) {
			echo $element->render();
			unset($element);
		} else {
			return $element->render();
		}
	}

}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class ViewException extends BasicException {
	/**
	 * @var View
	 */
	public $view;
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class ViewFileNotFoundException extends ViewException {
	public function __construct(View $view) {
		$this->view = $view;
		$message = sprintf('Unable to find view File \'%s\'', $this->view->filename);
		parent::__construct($message);
	}
}

/** 
 * Thrown if a layout directory was not found
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class ThemeNotFoundException extends ViewException {
	public function __construct(View $view) {
		$this->view = $view;
		$message = sprintf('Unable to find layout directory: \'%s\'.', $this->view->dir.$this->view->theme);
		parent::__construct($message);
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception 
 */
class LayoutFileNotFoundException extends ViewException {
	public function __construct(View $view) {
		$this->view = $view;
		$message = sprintf('Unable to find layout file: \'%s\'.', $this->view->filename);
		parent::__construct($message);
	}
}