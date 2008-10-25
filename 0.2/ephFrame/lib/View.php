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

// load required parent classes
require_once dirname(__FILE__).'/Hash.php';

/**
 * 	View (part of MVC)
 * 
 *	A view is something the user gets to see when he uses the app.
 * 
 * 	The View is generated from a layout file base and rendering a view created
 * 	from the controller name and action. This is a vary deep part of the ephFrame
 * 	framework.
 * 
 * 	A View can render {@link Element}s by using this kind of code in a view template.
 * 	Read more about elements in the docs of {@link Element}
 * 	<code>
 * 	// element must be located in /app/views/elements/elementName.php
 * 	echo $this->renderElement('elementName', array('dataVarName' => 'value');
 * 	// element must be located in /app/views/elements/sub/elementName.php
 * 	echo $this->renderElement('sub/elementName', array('dataVarName' => 'value');
 * 	</code>
 * 	
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 09.08.2007
 * 	@version 0.1
 *  @package ephFrame
 * 	@subpackage ephFrame.lib.component
 * 	@uses Hash
 * 	@uses Element
 */
abstract class View extends Hash implements Renderable {
	
	/**
	 *	Name of this view (this is used to get the
	 * 	Directory name)
	 * 	@var string
	 */
	protected $name;

	/**
	 *	@var string
	 */
	private $action;
	
	/**
	 * 	Extension for layout and view files
	 * 	this is some kind of security issue here not to use html
	 * 	@var string
	 */
	protected $templateExtension = 'php';
	
	/**
	 * 	Filename for this view, created in the __constructor
	 * 	@var string
	 */
	public $viewFilename;
	
	/**
	 *	Content type for this view that can be send to the client
	 * 	@var string
	 */
	public $contentType = 'text/plain';
	
	/**
	 *	View constructor
	 * 	@return View
	 */
	public function __construct($name, $action = 'index', $data = null) {
		if (is_object($data)) {
			$this->data = $data;
		} else {
			$this->data = new Hash($data);
		}
		// sanitize name
		$this->name = preg_replace('/[^-_\/a-zA-Z0-9]*/', '', $name);
		// sanitize action name
		$this->action = Sanitize::paranoid($action);
		// viewfilename
		$this->createViewFilename();
		return parent::__construct();
	}
	
	/**
	 * 	Returns the filename of this view
	 * 	@return string
	 */
	protected function createViewFilename () {
		if ($this->name == 'Layout') {
			$this->viewFilename = LAYOUT_DIR.$this->action.'.'.$this->templateExtension;	
		} else {
			$this->viewFilename = VIEW_DIR.lcfirst($this->name).'/'.lcfirst($this->action).'.'.$this->templateExtension;
			// if apps view does not exist, try to get view from ephFrame
			if (!file_exists($this->viewFilename)) {
				$ephFrameViewFile = FRAME_ROOT.'view/'.lcfirst($this->name).'/'.lcfirst($this->action).'.'.$this->templateExtension;
				if (file_exists($ephFrameViewFile)) {
					$this->viewFilename = $ephFrameViewFile;
				}
			}
		}
		if (!file_exists($this->viewFilename)) throw new ViewFileNotFoundException($this);
		return $this->viewFilename;
	}
	
	/**
	 * 	Renders the view by requiring a php file based on the view action name
	 * 
	 *	@throws ViewFileNotFoundException
	 * 	@throws LayoutFileNotFoundException
	 * 	@return string
	 */
	public function render() {
		if (!$this->beforeRender()) return null;
		ob_start();
		foreach($this->data->toArray() as $___key => $___val) {
			${$___key} = $___val;
		}
		// prevent key and val from manipulation
		unset($___key);
		unset($___val);
		require $this->viewFilename;
		$content = ob_get_clean();
		return $this->afterRender($content);
	}
	
	/**
	 * 	Echoes or returns the content of an {@link Element}.
	 * 
	 * 	This will try to render an element with the $elementName with the given	
	 * 	$data (data from the current controller is added automatically).
	 * 	If you want to disable the direct output of the element, pass $output
	 * 	as false.
	 * 
	 * 	Code from a view:
	 *	<code>
	 * 	$this->renderElement('mainMenu', array('menuEntries' => array(
	 * 		'main' => '/',
	 * 		'users' => '/users/'
	 * 	));
	 * 	</code>
	 * 
	 * 	@param string $elementName
	 * 	@param array $data
	 * 	@param boolean $output
	 * 	@return string
	 */
	public function renderElement($elementName, $data = array(), $output = true) {
		ephFrame::loadClass('ephFrame.lib.Element');
		if ($this->data instanceof Hash) {
			$data = array_merge($this->data->toArray(), $data);
		} elseif (is_array($this->data)) {
			$data = array_merge($this->data, $data);
		}
		$elementView = new Element($elementName, null, $data);
		if ($output) {
			echo $elementView->render();
		} else {
			return $elementView->render();
		}
	}

}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception 
 */
class ViewException extends BasicException {}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception 
 */
class ViewFileNotFoundException extends ViewException {
	public function __construct(View $view) {
		$this->view = $view;
		$message = 'Unable to find view File \''.$this->view->viewFilename.'\'';
		parent::__construct($message);
	}
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception 
 */
class LayoutFileNotFoundException extends ViewException {
	/**
	 * 	@var View
	 */
	public $view;
	public function __construct(View $view) {
		$this->view = $view;
		$message = 'Unable to find layout File '.$view->viewFilename;
		parent::__construct($message);
	}
}

?>