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

require_once dirname(__FILE__).'/HTTPRequest.php';

/**
 *	Controller Class
 * 	
 * 	Basic Application Controller class.
 * 	
 * 	// todo add some doc for this
 * 
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@version 0.1
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 10.08.2007
 */
abstract class Controller extends Object implements Renderable {
	
	/**
	 * 	Default controller action
	 *	@var string
	 */
	public $action = 'index';
	
	/**
	 *	optional name for the controller, used for the view folder
	 * 	@var string
	 */
	public $name;
	
	/**
	 *	@var HTTPRequest
	 */
	public $request;
	
	/**
	 * 	@var HTTPResponse
	 */
	public $response;
	
	/**
	 *	@var array(mixed)
	 */
	public $data = array(
		'pageTitle' => 'ephFrame'
	);
	
	/**
	 *	Default view layout for a controller
	 * 	@var string
	 */
	public $layout = 'default';
	
	/**
	 *	Default view class for a controller, if you want to use views that are
	 * 	stored in the app use paths like 'app.lib.MyCustomView'
	 * 	@var string
	 */
	public $viewClassName = 'HTMLView';
	
	/**
	 *	Array of models this controller uses
	 * 	@param array(string)
	 */
	public $uses = array();
	
	/**
	 *	Array of components used by this controller
	 * 	@var array(string)
	 */
	public $components = array('Session');
	
	/**
	 *	array of helper names used in the view when rendered
	 * 	@var array(string)
	 */
	public $helpers = array();
	
	/**
	 * 	Stores a number of form names that are used by this controller
	 * 	@var array(string)
	 */
	public $forms = array();
	
	/**
	 * 	Controller Constructor
	 * 	@param HTTPRequest $request
	 * 	@return Controller
	 */
	final public function __construct(HTTPRequest $request) {
		$this->beforeConstruct();
		$this->request = $request;
		$this->response = new HTTPResponse();
		$this->response->enableRenderHeaders = false;
		if (empty($this->name)) {
			if (!preg_match('/(.*)Controller/i', get_class($this), $found)) {
				$this->name = get_class($this);
			} else {
				$this->name = $found[1];
			}
		}
		// set controller name in the view
		$this->set('controller', $this->name);
		// get component list from parent class and merge them with this
		// controllers components and models ...
		foreach ($this->__parentClasses() as $parentClass) {
			$parentClassVars = get_class_vars($parentClass);
			if (isset($parentClassVars['uses'])) {
				$this->models = array_unique(array_merge($parentClassVars['uses'], $this->uses));
			}
			if (isset($parentClassVars['components'])) {
				$this->components = array_unique(array_merge($parentClassVars['components'], array_values($this->components)));
			}
			if (isset($parentClassVars['helpers'])) {
				$this->helpers = array_unique(array_merge($parentClassVars['helpers'], $this->helpers));
			}
			if (isset($parentClassVars['forms'])) {
				$this->forms = array_unique(array_merge($parentClassVars['forms'], $this->forms));
			}
		}
		// init components and helpers
		$this->initComponents();
		$this->initModels();
		$this->startUpComponents();
		$this->initForms();
		$this->initHelpers();
		$this->afterConstruct();
		return $this;
	}
	
	public function beforeConstruct() {}
	
	public function afterConstruct() {}
	
	/**
	 * 	Default create action
	 */
	public function create() {}
	
	public function delete() {}
	
	public function edit(Array $params = array()) {
		if (isset($params['id']) && in_array($this->name, $this->uses) && isset($this->{$this->name})) {
			$entry = $this->{$this->name}->findById($params['id']);
			$this->set($this->name, $entry);
			return $entry;
		}
	}
	
	/**
	 * 	Default view action
	 * 
	 * 	The default view action assumes that you want to view an entry from the
	 * 	model with an id. The id is machted by the router that calls this action.
	 * 	So for example you have the url [root]/User/view/23/ this action is
	 * 	called with the params array:
	 * 	<code>
	 * 	$params = array('id' => 23);
	 * 	</code>
	 * 	And will set a variable in the view named after the model used.
	 * 	
	 * 	So in our example you'll have in the view:
	 * 	<code>
	 * 	echo $User->id;
	 * 	</code>
	 *
	 * @param array $params
	 */
	public function view(Array $params = array()) {
		if (isset($params['id']) && in_array($this->name, $this->uses) && isset($this->{$this->name})) {
			$entry = $this->{$this->name}->findById($params['id']);
			$this->set($this->name, $entry);
			return $entry;
		}
	}
	
	/**
	 * 	Standard index action
	 * 
	 * 	This will get all entries from the model that matches to this controller.
	 * 	So if you have a UserController, this index action will provide _all_
	 * 	entries from the User Model (if assigned and working) to the view.
	 * 	You can overwrite or inherit this behavior in your child classes.
	 */
	public function index() {
		if (isset($this->{$this->name})) {
			$plural = Inflector::plural($this->name);
			$entries = $this->{$this->name}->getAll();
			$this->set($plural, $entries);
		}
	}
	
	/**
	 *	Default RSS Action tries to provide a Set of entries of the associated
	 * 	model from this controller in the view.
	 * 
	 * 	@return boolean
	 */
	public function rss($params = array()) {
		if (isset($this->{$this->name})) {
			$plural = Inflector::plural($this->name);
			$entries = $this->{$this->name}->getAll(null, null, null, 10);
			$this->set($plural, $entries);
			$this->layout = 'RSS';
			$this->viewClassName = 'XMLView';
		}
		return true;
	}
	
	/**
	 * 	Standard search action, searches for a $key $keyword match and lists
	 * 	all matches
	 *
	 * 	@param string $keyword
	 */
	public function search($keyword) {
		
	}
	
	/**
	 *	Sets or returns the view layout name assigned to this controller
	 * 	@param string $layout
	 * 	@return string
	 */
	public function layout($layout = null) {
		return $this->__getOrSet('layout', $layout);
	}
	
	/**
	 * 	Initiates all models associated by the {@link models} array to this
	 * 	controller and tries to establish a database connection using the
	 * 	data from /app/config/db.php
	 * 	@return boolean
	 */
	private function initModels() {
		if (!in_array($this->name, $this->uses)) {
			$this->uses[] = $this->name;
		}
		foreach($this->uses as $modelName) {
			$this->addModel($modelName);
		}
		return true;
	}
	
	public function addModel($modelName) {
		assert(is_string($modelName) && !empty($modelName));
//		if (in_array($modelName, $this->uses)) {
//			return true;
//		}
		if (strpos($modelName, ClassPath::$classPathDevider) === false) {
			$modelName = 'App.lib.model.'.$modelName;
			$className = ClassPath::className($modelName);
		} else {
			$className = $modelname;
		}
		try {
			ephFrame::loadClass($modelName);
			$this->{$className} = new $className();
			$this->{$className}->init();
			logg(Log::VERBOSE_SILENT, 'ephFrame: '.get_class($this).' loaded model \''.$className.'\'');
		} catch (ephFrameClassFileNotFoundException $e) {
			if ($modelName == $this->name) throw new $e;
		}
		return true;
	}
	
	/**
	 * 	Initiate Components
	 * 
	 * 	This method iterates the list of component names and loads, initates
	 * 	and startsup the component in this order:
	 * 	 # load component class (error if not found)
	 *   # init component right after loading
	 *   # attach component to this controller ($this->$componentname)
	 *  After all components, and components that are specified in the components
	 * 	all components receive the startup signal.
	 * 	@return boolean
	 */
	protected function initComponents() {
		logg(Log::VERBOSE_SILENT, 'ephFrame: '.get_class($this).' adds components: \''.implode(', ', $this->components).'\'');
		// add and init every component set in in {@link components}
		foreach ($this->components as $componentName) {
			$this->addComponent($componentName, false);
		}
		return true;
	}
	
	/**
	 * 	Tests if this controller has a {@link Component} attached
	 * 	@param string $componentName
	 * 	@return boolean
	 */
	public function hasComponent($componentName) {
		return in_array($componentName, $this->components);
	}
	
	/**
	 * 	Loads and Adds a new {@link Component} to the Controller at run-time.
	 * 	<code>
	 * 	class TestController {
	 * 		public function testI28n() {
	 * 			$this->addComponent('I28n');
	 * 		}
	 *  }
	 * 	</code>
	 * 	@param string $componentName
	 * 	@param boolean $startUp Fires the startup signal to the component
	 * 	@return boolean
	 */
	public function addComponent($componentName, $startUp = true) {
		assert(is_string($componentName) && !empty($componentName));
		if (!in_array($componentName, $this->components)) {
			$this->components[] = $componentName;
		}
		// extract component class name
		$className = ClassPath::className($componentName);
		// try app and frame paths
		if (!class_exists($className)) {
			loadComponent($componentName);
		}
		// attach component to controller
		if (!isset($this->$className)) {
			$this->{$componentName} = new $className();
			$this->{$componentName}->controller = $this;
			if (method_exists($this->{$componentName}, 'init')) {
				$this->{$className}->init($this);
			}
			if ($startUp) {
				$this->{$className}->startup();
			}
		}
		return true;
	}
	
	/**
	 *	At this point all components that are used by this controller should
	 * 	be added to the component list (also added by other components) and
	 * 	get the startup signal now.
	 * 	@return boolean
	 */
	public function startUpComponents() {
		foreach($this->components as $componentName) {
			$className = ClassPath::className($componentName);
			$this->{$className}->startup();
		}
		return true;
	}
	
	/**
	 * 	Initiates all {@link Helper}s listed in the {@link helpers} property
	 * 	of the controller and returns true.
	 * 	@return boolean
	 */
	private function initHelpers() {
		assert(is_array($this->helpers));
		foreach($this->helpers as $helperName) {
			$this->addHelper($helperName);
		}
		return true;
	}
	
	/**
	 * 	Adds an other helper to the controller view data on run-time.
	 * 
	 * 	So you cann add Helpers in controller actions if you don't want to have
	 * 	them in the $helpers array of the controller.
	 * 	<code>
	 * 	// in a controller action
	 * 	$this->addHelper('HTML');
	 * 	</code>
	 * 	@param string $helperName Classpath or Classname of Helper Class
	 * 	@return boolean
	 */
	public function addHelper($helperName) {
		assert(is_string($helperName) && !empty($helperName));
		// extract component class name
		$className = ClassPath::className($helperName);
		// try app and frame paths
		loadHelper($helperName);
		// verbose log message
		logg(Log::VERBOSE_SILENT, 'ephFrame: '.get_class($this).' loaded helper '.$helperName.' successfully');
		// attach component to controller
		$this->set($className, new $className($this));
		return true;
	}
	
	private function initForms() {
		foreach($this->forms as $formName) {
			$this->addForm($formName);
		}
		foreach($this->forms as $formName) {
			$this->{$formName}->startup();
			$this->{$formName}->configure();
		}
		return $this;
	}
	
	/**
	 *	Adds an other form to the controller
	 * 	@param string $formName
	 * 	@return Controller
	 */
	public function addForm($formName) {
		if (!class_exists($formName)) {
			ephFrame::loadClass('app.lib.component.Form.'.$formName);
		}
		$this->{$formName} = new $formName();
		$this->{$formName}->init($this);
		logg(Log::VERBOSE_SILENT, 'ephFrame: '.get_class($this).' loaded form '.$formName.'');
		return $this;
	}
	
	/**
	 *	Set a variable for the view
	 * 	@param string $name
	 * 	@param mixed $value
	 */
	public function set($name, $value) {
		$this->data[$name] = $value;
		return true;
	}
	
	/**
	 *	Sets an other action for this controller, this affects the view that
	 * 	is used and also calls the method that has the same name as the action
	 * 	@param string $action
	 * 	@param array(mixed) $params
	 * 	@return boolean
	 */
	public function action($action, Array $params = array()) {
		assert(is_string($action) && !empty($action));
		logg(Log::VERBOSE, 'ephFrame: '.get_class($this).' changed action from \''.$this->action.'\' to \''.$action.'\'');
		$this->action = $action;
		$this->set('action', $this->action);
		if (method_exists($this, $action)) {
			$this->$action($params);
		}
		return true;
	}
	
	/**
	 *	Disables browser cache for this controller by sending not caching
	 * 	header commands to the client
	 * 	// todo set the headers in the response object instead of sending directly
	 * 	@return boolean true
	 */
	public function disableCache() {
		logg(Log::VERBOSE_SILENT, 'Controller disableCache called in \''.get_class($this).'\'');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		return true;
	}
	
	/**
	 * 	The Rendering action of the controller happens after the action was
	 * 	called.
	 * 
	 *	@return string
	 */
	public function render() {
		if (!$this->beforeRender()) return false;
		// load view class if available
		if (!strpos($this->viewClassName, '.')) {
			ephFrame::loadClass('ephFrame.lib.'.$this->viewClassName);
		} else {
			ephFrame::loadClass($this->viewClassName);
		}
		// render the view part
		$view = new $this->viewClassName($this->name, &$this->action, $this->data);
		$viewRendered = $view->render();
		// wrap layout around view
		if (!empty($this->layout)) {
			$layoutViewVars = array('content' => $viewRendered);
			$layoutView = new $this->viewClassName('layouts', $this->layout, array_merge($this->data, $layoutViewVars));
			$content = $layoutView->render();
		} else {
			$content = $viewRendered;
		}
		$this->response->body = $this->afterRender($content);
		// @todo add this to request/response
		if (!preg_match('/gzip/i', $_SERVER['HTTP_ACCEPT_ENCODING']) && $this->response->enableGZipCompression) {
			$this->response->enableGZipCompression = false;
		}
		$this->response->header->set('Content-Type', $view->contentType.'; charset=utf-8');
		$rendered = $this->response->render();
		$this->response->header->send();
		return $rendered;
	}
	
	/**
	 * 	This is a hook for everything that should happen before Rendering
	 * 
	 * 	Some examples:
	 * 	# add additional keywords to the header
	 *  # render menues before Rendering
	 *  # Sanitizing data
	 * 
	 * 	This method should return true if you want the controller to render the
	 * 	output.
	 *
	 * 	@return boolean
	 */
	public function beforeRender() {
		return true;
	}
	
	/**
	 * 	This is a hook for your own after rendering logic.
	 * 	
	 * 	So you can set every character to lowerspace or beautify the html code
	 * 	that is echoed.
	 * 
	 * 	This method should always return a string, the string that is finally
	 * 	send to the client.
	 * 	
	 * 	@param string $rendered
	 * 	@return string
	 */
	public function afterRender($rendered) {
		// if we're in debugging mode we add the sql history dump to the view
		// content (this can be overwritten in the AppController.
		if (Registry::get('DEBUG') >= DEBUG_DEBUG && class_exists('QueryHistory')) {
			$debugOutput =
				'Compile Time: '.ephFrame::compileTime(6).'s'.LF.
				'Memory Usage: '.ephFrame::memoryUsage(true).' ('.ephFrame::memoryUsage().' Bytes)'.LF.LF.
				'DB QUERY HISTORY'.LF.'----------------'.LF.QueryHistory::getInstance()->render();
			if ($this->viewClassName == 'HTMLView') {
				$rendered .= '<pre class="debugOutput">'.nl2br($debugOutput).'</pre>';	
			} else {
				$rendered .= $debugOutput;
			}
		}
		return $rendered;
	}
	
	/**
	 *	Redirects to an other url
	 * 	<code>
	 * 	// for example direct to user login and exit
	 * 	$this->redirect('/user/login/', null, true);
	 * 	</code>
	 * 	@param string $url
	 * 	@param integer $status HTTP 1.1 status code
	 * 	@param boolean $exit exit after redirect
	 */
	public function redirect($url, $status = null, $exit = false) {
		if (!class_exists('HTTPStatusCode')) ephFrame::loadClass('ephFrame.lib.HTTPStatusCode');
		if ($url !== null) {
			header('Location: '.$url);
		}
		if (!empty($status)) {
			if (isset(HTTPStatusCode::$statusCodes[$status])) {
				header(sprintf('HTTP/1.1 %s %s;', $status, HTTPStatusCode::$statusCodes[$status]));	
			}
		}
		if ($exit) {
			exit();
		}
		return true;
	}
	
	/**
	 *	Returns the refererrer submitted by the client if found
	 * 	Set $local to true to only use internal urls (external urls will be
	 * 	ignored)
	 * 	@param string $default default referer
	 * 	@param boolean $local Use only local referers
	 * 	@return string
	 */
	public function referer($default = false, $local = true) {
		if (isset($_SERVER['HTTP_REFERER']) && empty($_SERVER['HTTP_REFERER'])) {
			// return refere only if in local domain
			if (!$local) {
				return $_SERVER['HTTP_REFERER'];
			} elseif (preg_match('/^(http:\/\/|)'.str_replace('.', '\.', $_SERVER['HTTP_HOST']).'/', $_SERVER['REFERER'])) {
				return $_SERVER['HTTP_REFERER'];
			}
		}
		return $default;
	}

}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ControllerException extends BasicException {
	
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ControllerMissingActionException extends ControllerException {
	public function __construct(Controller $controller, $action = null) {
		$message = get_class($controller).' misses ';
		if (empty($message) && func_num_args() == 1) {
			$message .= ' the action named '.$controller->action;
		} else {
			$message .= ' the action named '.$action;
		}
		parent::__construct($message);
	}
}

?>