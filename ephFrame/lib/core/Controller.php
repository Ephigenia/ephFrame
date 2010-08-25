<?php

/**
 * ephFrame: http://code.marceleichner.de/project/ephFrame/
 * Copyright 2007+, Ephigenia M. Eichner, Kopernikusstr. 8, 10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright 2007+, Ephigenia M. Eichner
 * @link http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

class_exists('HTTPRequest') or require dirname(__FILE__).'/../net/HTTP/HTTPRequest.php';

/**
 * Controller Class
 * 
 * Basic Application Controller class.
 * 
 * // todo add some doc for this
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @version 0.1
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 10.08.2007
 */
abstract class Controller extends Object
{	
	/**
	 * Default controller action
	 * @var string
	 */
	public $action = 'index';
	
	/**
	 * optional name for the controller, used for the view folder
	 * @var string
	 */
	public $name;
	
	/**
	 * @var HTTPRequest
	 */
	public $request;
	
	/**
	 * @var HTTPResponse
	 */
	public $response;
	
	/**
	 * @var Hash
	 */
	public $data = array(
		'pageTitle' => 'ephFrame'
	);
	
	/**
	 * Stores the parameters from the router
	 * @var array(mixed)
	 */
	public $params = array();
	
	/**
	 * Default view layout for a controller
	 * @var string
	 */
	public $layout = 'default';
	
	/**
	 * Optional Theme name that should be used
	 * @var string
	 */
	public $theme = false;
	
	/**
	 * Default view class for a controller, if you want to use views that are
	 * stored in the app use paths like 'app.lib.MyCustomView'
	 * @var string
	 */
	public $viewClassName = 'ephFrame.lib.view.View';
	
	/**
	 * Array of models this controller uses
	 * @param array(string)
	 */
	public $uses = array();
	
	/**
	 * Array of components used by this controller
	 * @var array(string)
	 */
	public $components = array();
	
	/**
	 * array of helper names used in the view when rendered
	 * @var array(string)
	 */
	public $helpers = array();
	
	/**
	 * Stores a number of form names that are used by this controller
	 * @var array(string)
	 */
	public $forms = array();
	
	/**
	 * Controller Constructor
	 * @param HTTPRequest $request
	 * @return Controller
	 */
	final public function __construct(HTTPRequest $request = null) 
	{
		// get component list from parent class and merge them with this
		// controllers components and models ...
		foreach ($this->__parentClasses() as $parentClass) {
			$this->__mergeParentProperty('uses');
			$this->__mergeParentProperty('components');
			$this->__mergeParentProperty('helpers');
			$this->__mergeParentProperty('forms');
			$this->__mergeParentProperty('data');
		}
		$this->beforeConstruct();
		$this->data = new Hash($this->data);
		$this->request = !empty($request) ? $request : new HTTPRequest();
		$this->response = new HTTPResponse();
		$this->response->enableRenderHeaders = false;
		if (empty($this->name) && !($this->name = preg_match_first(get_class($this), '@(.*)Controller@i'))) {
			$this->name = get_class($this);
		}
		// init components and helpers
		$this->initComponents();
		$this->initModels();
		foreach($this->components as $component) {
			$component->startUp();
		}
		$this->initForms();
		$this->initHelpers();
		$this->afterConstruct();
		return $this;
	}
	
	protected function beforeConstruct() 
	{
		$this->registerCallback('beforeRender', array($this, 'beforeRender'));
		$this->registerCallback('beforeAction', array($this, 'beforeAction'));
		return true;
	}
	
	protected function afterConstruct() 
	{
		$this->registerCallback('afterRender', array($this, 'afterRender'));
		$this->registerCallback('afterAction', array($this, 'afterAction'));
		return true;
	}
	
	public function create() 
	{
		if (isset($this->Scaffold)) {
			return $this->Scaffold->create();
		}
		return true;
	}
	
	public function delete($id = null) 
	{
		if (isset($this->Scaffold)) {
			$this->Scaffold->delete($id);
		}
		return true;
	}
	
	public function edit($id = null) 
	{
		if (isset($this->Scaffold)) {
			return $this->Scaffold->edit($id);
		}
		return true;
	}
	
	/**
	 * Default view action
	 * @param integer $id
	 */
	public function view($id = null) 
	{
		if (isset($this->Scaffold)) {
			return $this->Scaffold->view($id);
		}
		return true;
	}
	
	/**
	 * Standard index action
	 * 
	 * This will get all entries from the model that matches to this controller.
	 * So if you have a UserController, this index action will provide _all_
	 * entries from the User Model (if assigned and working) to the view.
	 * You can overwrite or inherit this behavior in your child classes.
	 */
	public function index()
	{
		if (isset($this->Scaffold)) {
			return $this->Scaffold->index();
		}
		return true;
	}
	
	/**
	 * Initiates all models associated by the {@link models} array to this
	 * controller and tries to establish a database connection using the
	 * data from /app/config/db.php
	 * @return boolean
	 */
	private function initModels()
	{
		if (!in_array($this->name, $this->uses) && ClassPath::exists('App.lib.model.'.$this->name)) {
			$this->uses[] = $this->name;
		}
		foreach($this->uses as $modelName) {
			$this->addModel($modelName);
		}
		return true;
	}
	
	public function addModel($modelName) 
	{
		try {
			$this->{$modelName} = Library::create($modelName);;
		} catch (ClassPathMalformedException $e) {
			$this->{$modelName} = Library::create('App.lib.model.'.$modelName);
		}
		if ($this->{$modelName} instanceOf Model) {
			$this->{$modelName}->init($this);
		}
		return true;
	}
	
	/**
	 * Initiate Components
	 * 
	 * This method iterates the list of component names and loads, initates
	 * and startsup the component in this order:
	 *  # load component class (error if not found)
	 * # init component right after loading
	 * # attach component to this controller ($this->$componentname)
	 * After all components, and components that are specified in the components
	 * all components receive the startup signal.
	 * @return boolean
	 */
	protected function initComponents()
	{
		foreach ($this->components as $index => $name) {
			unset($this->components[$index]);
			$this->addComponent($name, false);
		}
		return true;
	}
	
	/**
	 * Loads and Adds a new {@link Component} to the Controller at run-time.
	 * 
	 * You can use this to dynamicly add components to controllers that should
	 * only be available on some certain actions.
	 * <code>
	 * class TestController extends AppController
	 * {
	 * 	public function testI28n() {
	 * 		$this->addComponent('I28n');
	 * 	}
	 * }
	 * </code>
	 * 
	 * @param string $component
	 * @param boolean $startUp
	 * @return boolean
	 */
	public function addComponent($name, $startUp = true)
	{
		if (!isset($this->components[$name])) {
			try {
				$classname = Library::load($name);
			} catch (ClassPathMalformedException $e) {
				try {
					$classname = Library::load('app.lib.component.'.$name);
				} catch (LibraryFileNotFoundException $e) {
					$classname = Library::load('ephFrame.lib.component.'.$name);
				}
			}
			$this->{$classname} = new $classname();
			if (method_exists($this->{$classname}, 'init')) {
				$this->{$classname}->init($this);
			}
			if ($startUp && method_exists($this->{$classname}, 'startup')) {
				$this->{$classname}->startup();
			}
			$this->components[$name] = $this->{$classname};
		}
		return $this->components[$name];
	}
	
	/**
	 * Initiates all {@link Helper}s listed in the {@link helpers} property
	 * of the controller and returns true.
	 * @return boolean
	 */
	protected function initHelpers()
	{
		Library::load('ephFrame.lib.view.helper.Helper');
		Library::load('app.lib.helper.AppHelper');
		foreach($this->helpers as $index => $helperName) {
			unset($this->helpers[$index]);
			$this->addHelper($helperName);
		}
		return true;
	}
	
	/**
	 * Adds an other helper to the controller view data on run-time.
	 * 
	 * So you cann add Helpers in controller actions if you don't want to have
	 * them in the $helpers array of the controller.
	 * <code>
	 * // in a controller action
	 * $this->addHelper('HTML');
	 * </code>
	 * @param string $helperName Classpath or Classname of Helper Class
	 * @return boolean
	 */
	public function addHelper($name) 
	{
		// try app and frame paths
		if (!isset($this->helpers[$name])) {
			try {
				$classname = Library::load($name);
			} catch (ClassPathMalformedException $e) {
				try {
					$classname = Library::load('App.lib.helper.'.$name);
				} catch (LibraryFileNotFoundException $e) {
					$classname = Library::load('ephFrame.lib.view.helper.'.$name);
				}
			}
			$Helper = new $classname($this);
			$this->helpers[$name] = $Helper;
		}
		return $this->helpers[$name];
	}
	
	private function initForms()
	{
		if (!empty($this->forms)) {
			// add all forms as objects
			foreach($this->forms as $index => $formName) {
				unset($this->forms[$index]);
				$this->addForm($formName);
			}
			foreach($this->forms as $Form) {
				$Form->startup($this)->configure();
			}
		} elseif (ClassPath::exists('app.lib.component.Form.'.$this->name.'Form')) {
			$this->addForm($this->name.'Form');
			$this->{$this->name.'Form'}->startUp($this)->configure();
		}
		return $this;
	}
	
	/**
	 * Adds an other form to the controller
	 * @param string $name
	 * @return Form
	 */
	public function addForm($name) 
	{
		if (!isset($this->forms[$name])) {
			try {
				$classname = Library::load($name);
			} catch (ClassPathMalformedException $e) {
				$classname = Library::load('App.lib.component.form.'.$name);
			}
			$classname = ClassPath::className($name);
			$this->{$classname} = new $classname($name);;
			$this->{$classname}->init($this);
			if ($this->action !== 'index') {
				$this->{$classname}->startup($this)->configure();
			}
			$this->forms[$name] = $this->{$name};
		}
		return $this->forms[$name];
	}
	
	/**
	 * Sets an other action for this controller, this affects the view that
	 * is used and also calls the method that has the same name as the action
	 * 
	 * @param string $action
	 * @param array(mixed) $params
	 * @return boolean
	 */
	public function action($action, Array $params = array()) 
	{
		$this->action = $action;
		// additional parameters
		$this->params = array_merge($this->params, $params, $this->request->data);
		// action and controller name set for view
		$this->data->set('action', $this->action);
		$this->data->set('controller', $this->name);
		foreach (array('layout', 'theme') as $v) {
			if (isset($this->params[$v])) $this->{$v} = $this->params[$v];
		}
		$arguments = array_diff_key($params, array('controller' => 0, 'action' => 0, 'path' => 0, 'controllerPrefix' => 0, 'prefix' => 0, 'layout' => 0));
		// before action, action and after action
		$beforeActionResult = $this->callback('beforeAtion', array($action, $params));
		if (method_exists($this, $action)) {
			if (!$beforeActionResult || $this->callMethod($action, $arguments) === false) {
				return $this->error(404);
			}
		}
		$this->callback('afterRender', array($this->action));
		return $this;
	}
	
	public function error($statusCode)
	{
		$params = func_get_args();
		// load error controller either from app or ephframe’s error controller
		if (!class_exists('ErrorController')) {
			if (ClassPath::exists('App.lib.controller.AppErrorController')) {
				Library::load('App.lib.controller.AppErrorController');
			} else {
				Library::load('ephFrame.lib.core.ErrorController');
			}
		}
		$errorController = new ErrorController($this->request);
		$errorController->theme = $this->theme;
		$errorController->action('error', $params);
		die($errorController->render());
	}
	
	/**
	 * The Rendering action of the controller happens after the action was
	 * called.
	 * @return string
	 */
	public function render() 
	{
		if (!$this->callback('beforeRender')) {
			return false;
		}
		// use view class to render controller result
		$view = Library::create($this->viewClassName, array($this->name, $this->action, $this->data));
		// @todo refactor the usage of theme here
		$view->theme = $this->theme;
		$content = $view->render();
		// wrap layout around view
		if (!empty($this->layout)) {
			$this->data->set('content', $content);
			$layout = Library::create($this->viewClassName, array('layout', $this->layout, $this->data));
			$layout->theme = $this->theme;
			$content = $layout->render();
		}
		$this->response->body = $this->callback('afterRender', array($content));
		// set content header if not allready set
		if ($this->response->header->isEmpty('Content-Type')) {
			$this->response->header->set('Content-Type', $view->contentType.'; charset=utf-8');
		}
		$rendered = $this->response->render();
		$this->response->header->send();
		return $rendered;
	}
	
	/**
	 * This is a hook for everything that should happen before Rendering
	 * 
	 * Some examples:
	 * # add additional keywords to the header
	 * # render menues before Rendering
	 * # Sanitizing data
	 * 
	 * This method should return true if you want the controller to render the
	 * output.
	 *
	 * @return boolean
	 */
	public function beforeRender() 
	{
		foreach(array_merge($this->components, $this->helpers, $this->forms) as $object) {
			$this->data->set(get_class($object), $object);
		}
		$this->data->set('theme', $this->theme);
		return true;
	}
	
	/**
	 * Called everytime a controller gets a new action. called before the action
	 * is called and after all component are called.
	 * @param string $action action that is to be called
	 * @return boolean
	 */
	public function beforeAction() 
	{
		if (method_exists($this, 'before'.ucFirst($this->action))) {
			if (!$this->callMethod('before'.ucFirst($this->action))) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Called after action is done
	 * @param string $action action that is done
	 * @return boolean
	 */
	public function afterAction() 
	{
		if (method_exists($this, 'after'.ucFirst($this->action))) {
			$this->callMethod('after'.ucFirst($this->action));
		}
		return true;
	}
	
	/**
	 * This is a hook for your own after rendering logic.
	 * 
	 * So you can set every character to lowerspace or beautify the html code
	 * that is echoed.
	 * 
	 * This method should always return a string, the string that is finally
	 * send to the client.
	 * 
	 * @param string $content
	 * @return string
	 */
	public function afterRender($content)
	{
		return $content;
	}
	
	/**
	 * Send redirect header to client
	 * 
	 * This will send a redirect header directing to $url with the http $status
	 * code and exit php if you pass $exit = true.<br />
	 * The status code must be a valid HTTP-Statuscode or 'perm', 'permanent',
	 * 'p' for permanent moved (301), or 'tmp', 'temp', 't' for temporary
	 * redirect (307).<br />
	 * The status code will not be send if it's invalid.<br />
	 * This will overwrite previously send location and status header.<br />
	 *
	 * It will also call beforeRedirect before redirecting the user with all
	 * the parameters.
	 * 
	 * <code>
	 * // for example direct to user login and exit
	 * $this->redirect('/user/login/', null, true);
	 * // skip to an other url
	 * $this->redirect('http://code.marceleichner.de/', 'p', true);
	 * </code>
	 * 
	 * @param string $url
	 * @param integer $status HTTP 1.1 status code
	 * @param boolean $exit exit after redirect
	 * @return boolean
	 */
	public function redirect($url, $status = 'p', $exit = true) 
	{
		if (!$this->beforeRedirect($url, $status, $exit)) {
			return false;
		}
		$this->callback('beforeRedirect', array($url, $status, $exit));
		header('Location: '.$url, true);
		if (!empty($status)) {
			class_exists('HTTPStatusCode') or Library::load('ephFrame.lib.HTTPStatusCode');
			if (in_array($status, array('p', 'permanent', 'perm'))) {
				$status = 301;
			}
			if (in_array($status, array('t', 'tmp', 'temporary'))) {
				$status = 307;
			}
			if (isset(HTTPStatusCode::$statusCodes[$status])) {
				header(sprintf('HTTP/1.1 %s %s;', $status, HTTPStatusCode::$statusCodes[$status]), true);	
			}
		}
		if ($exit) {
			exit();
		}
		return true;
	}
	
	/**
	 * Callback that is called right before the {@link redirect] action
	 * sends the redirect header.
	 * You can implement on-redirect logic here
	 * @return boolean
	 */
	public function beforeRedirect($url, $status = 'p', $exit = true) 
	{
		return true;
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ControllerException extends BasicException 
{ }

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ControllerMissingActionException extends ControllerException 
{
	public function __construct(Controller $controller, $action = null) 
	{
		$message = get_class($controller).' misses ';
		if (empty($message) && func_num_args() == 1) {
			$message .= ' the action named '.$controller->action;
		} else {
			$message .= ' the action named '.$action;
		}
		parent::__construct($message);
	}
}