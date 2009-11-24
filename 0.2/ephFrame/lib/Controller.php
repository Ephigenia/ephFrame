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

class_exists('HTTPRequest') or require dirname(__FILE__).'/HTTPRequest.php';

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
abstract class Controller extends Object implements Renderable
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
	public $viewClassName = 'HTMLView';
	
	/**
	 * Array of models this controller uses
	 * @param array(string)
	 */
	public $uses = array();
	
	/**
	 * Array of components used by this controller
	 * @var array(string)
	 */
	public $components = array('Session');
	
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
	final public function __construct(HTTPRequest $request) {
		// get component list from parent class and merge them with this
		// controllers components and models ...
		foreach ($this->__parentClasses() as $parentClass) {
			$parentClassVars = get_class_vars($parentClass);
			$this->__mergeParentProperty('uses');
			$this->__mergeParentProperty('components');
			$this->__mergeParentProperty('helpers');
			$this->__mergeParentProperty('forms');
			$this->__mergeParentProperty('data');
		}
		$this->beforeConstruct();
		$this->data = new Hash($this->data);
		$this->request = $request;
		$this->response = new HTTPResponse();
		$this->response->enableRenderHeaders = false;
		if (empty($this->name)) {
			if (!($this->name = preg_match_first(get_class($this), '@(.*)Controller@i'))) {
				$this->name = get_class($this);
			}
		}
		// set controller name in the view
		$this->set('controller', $this->name);
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
	 * Default create action
	 */
	public function create() {}
	
	/**
	 * @param integer $id
	 */
	public function delete($id = null) {
		$id = ($id === null) ? (int) @$this->params['id'] : $id;
		if ($id > 0 && isset($this->{$this->name})) {
			if (!$entry = $this->{$this->name}->findById($id)) {
				$this->name = 'error';
				$this->action('404', array());
				return false;
			}
			return $entry->delete();
		}
	}
	
	/**
	 * Standard edit action method
	 * @param integer $id
	 */
	public function edit($id = null) {
		$id = (int) ($id === null) ? $this->params['id'] : $id;
		if (!empty($id) && isset($this->{$this->name})) {
			if (!($model = $this->{$this->name}->findById($id))) {
				return false;
			}
			$this->data->set($this->name, $model);
			// if form is also attached, fill form data
			if (isset($this->{$this->name.'Form'})) {
				$this->{$this->name.'Form'}->fillModel($model);
			}
			return $model;
		}
	}
	
	/**
	 * Default view action
	 * @param integer $id
	 */
	public function view($id = null) {
		$id = (int) ($id === null) ? $this->params['id'] : $id;
		if (!empty($id) && isset($this->{$this->name})) {
			if (!($this->{$this->name} = $this->{$this->name}->findById($id))) {
				return false;
			} else {
				$this->data->set($this->name, $this->{$this->name});
				return $this->{$this->name};
			}
		}
	}
	
	/**
	 * Standard index action
	 * 
	 * This will get all entries from the model that matches to this controller.
	 * So if you have a UserController, this index action will provide _all_
	 * entries from the User Model (if assigned and working) to the view.
	 * You can overwrite or inherit this behavior in your child classes.
	 */
	public function index() {
		if (isset($this->{$this->name})) {
			$page = intval((@$this->params['page'] > 1) ? $this->params['page'] : 1);
			$entries = $this->{$this->name}->findAll(null, null, ($page-1) * $this->{$this->name}->perPage, $this->{$this->name}->perPage);
			$this->data->set(Inflector::plural($this->name), $entries);
			if ($this->{$this->name}->perPage > 0) {
				$pagination = $this->{$this->name}->paginate($page);
				$pagination['url'] = Router::getRoute($this->name.'Paged');
				$this->set('pagination', $pagination);
			}
			if (!$entries) {
				return true;
			}
			return $entries;
		}
	}
	
	/**
	 * Default RSS Action tries to provide a Set of entries of the associated
	 * model from this controller in the view.
	 * 
	 * @return boolean
	 */
	public function rss() {
		if (isset($this->{$this->name})) {
			$entries = $this->{$this->name}->findAll(null, null, 0, 20);
			$entries = $this->{$this->name}->findAll(null, null, null, $this->{$this->name}->perPage);
			$this->set(Inflector::plural($this->name), $entries);
			$this->set('RSSURL', Registry::get('WEBROOT_URL').$this->request->get('__url'));
			$this->layout = 'rss';
			$this->viewClassName = 'XMLView';
			Registry::set('DEBUG', DEBUG_DEVELOPMENT);
		}
		return true;
	}
	
	/**
	 * Standard search action, searches for a $key $keyword match and lists
	 * all matches
	 *
	 * @param string $keyword
	 */
	public function search($keyword = null, $fields = array()) {
		if (empty($keyword)) return true;
		$searchTerm = '%'.$keyword.'%';
		$searchTermQuoted = DBQuery::quote($searchTerm);
		if (isset($this->{$this->name}) && strlen($keyword) > 0) {
			$this->set('keyword', $keyword);
			$conditions = array();
			foreach($this->{$this->name}->structure as $fieldInfo) {
				if (count($fields) > 0 && !in_array($fieldInfo->name, $fields)) continue;
				$conditions[] = $this->{$this->name}->name.'.'.$fieldInfo->name.' LIKE '.$searchTermQuoted.' OR';
			}
			if (empty($conditions)) {
				return new IndexedArray();
			}
			$page = (isset($this->params['page'])) ? $this->params['page'] : 1;
			$pagination = $this->{$this->name}->paginate($page, null, $conditions);
			$pagination['url'] = Router::getRoute(lcfirst($this->name).'SearchPaged', array('term' => $keyword));
			$this->set('pagination', $pagination);
			$results = $this->{$this->name}->findAll($conditions, null, ($page-1) * $this->{$this->name}->perPage, $this->{$this->name}->perPage);
			$this->set(Inflector::plural($this->name), $results);
			return $results;
		}
		return true;	
	}
	
	/**
	 * Sets or returns the view layout name assigned to this controller
	 * @param string $layout
	 * @return string
	 */
	public function layout($layout = null) {
		return $this->__getOrSet('layout', $layout);
	}
	
	/**
	 * Initiates all models associated by the {@link models} array to this
	 * controller and tries to establish a database connection using the
	 * data from /app/config/db.php
	 * @return boolean
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
		if (func_num_args() > 1) {
			$args = func_get_args();
			foreach($args as $arg) $this->addModel($arg);
			return true;
		}
		assert(is_string($modelName) && !empty($modelName));
		$classPath = $modelName;
		if (strpos($modelName, ClassPath::$classPathDevider) === false) {
			$classPath = 'App.lib.model.'.$modelName;
			$modelName = ClassPath::className($classPath);
		}
		try {
			if (!class_exists($modelName)) {
				ephFrame::loadClass($classPath);
			}
			if (is_subclass_of($modelName, 'Model')) {
				$this->{$modelName} = new $modelName();
				$this->{$modelName}->init($this);
				logg(Log::VERBOSE_SILENT, 'ephFrame: '.get_class($this).' loaded model \''.$modelName.'\'');
			}
		} catch (ephFrameClassFileNotFoundException $e) {
			if ($modelName != $this->name) throw $e;
		}
		return true;
	}
	
	public function hasModel($modelName) {
		return in_array($modelName, $this->uses);
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
	protected function initComponents() {
		logg(Log::VERBOSE_SILENT, 'ephFrame: '.get_class($this).' adds components: \''.implode(', ', $this->components).'\'');
		// add and init every component set in in {@link components}
		foreach ($this->components as $componentName) {
			$this->addComponent($componentName, false);
		}
		return true;
	}
	
	/**
	 * Tests if this controller has a {@link Component} attached
	 * @param string $componentName
	 * @return boolean
	 */
	public function hasComponent($componentName) {
		return in_array($componentName, $this->components);
	}
	
	/**
	 * Loads and Adds a new {@link Component} to the Controller at run-time.
	 * 
	 * You can use this to dynamicly add components to controllers that should
	 * only be available on some certain actions.
	 * <code>
	 * class TestController extends AppController {
	 * 	public function testI28n() {
	 * 		$this->addComponent('I28n');
	 * 	}
	 * }
	 * </code>
	 * 
	 * @param string $componentName
	 * @param boolean $startUp Fires the startup signal to the component
	 * @return boolean
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
			ephFrame::loadComponent($componentName);
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
	 * At this point all components that are used by this controller should
	 * be added to the component list (also added by other components) and
	 * get the startup signal now.
	 * @return boolean
	 */
	public function startUpComponents() {
		foreach($this->components as $componentName) {
			$className = ClassPath::className($componentName);
			$this->{$className}->startup();
		}
		return true;
	}
	
	/**
	 * Initiates all {@link Helper}s listed in the {@link helpers} property
	 * of the controller and returns true.
	 * @return boolean
	 */
	private function initHelpers() {
		assert(is_array($this->helpers));
		foreach($this->helpers as $helperName) {
			$this->addHelper($helperName);
		}
		foreach($this->helpers as $helperName) {
			$className = ClassPath::className($helperName);
			$this->data->get($className)->startup();
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
	public function addHelper($helperName) {
		// extract component class name
		$className = ClassPath::className($helperName);
		// try app and frame paths
		loadHelper($helperName);
		// verbose log message
		logg(Log::VERBOSE_SILENT, 'ephFrame: '.get_class($this).' loaded helper '.$helperName.' successfully');
		// attach component to controller
		$this->data->set($className, new $className($this));
		return true;
	}
	
	private function initForms() {
		// add form name of this controller if class is found
		if (!in_array($this->name.'Form', $this->forms) && ClassPath::exists('app.lib.component.Form.'.$this->name.'Form')) {
			$this->forms[] = $this->name.'Form';
		}
		// add all forms as objects
		foreach($this->forms as $formName) {
			$this->addForm($formName);
		}
		// startup and init all forms
		foreach($this->forms as $formName) {
			$this->{$formName}->startup($this);
			$this->{$formName}->configure();
		}
		return $this;
	}
	
	/**
	 * Adds an other form to the controller
	 * @param string $formName
	 * @return Controller
	 */
	public function addForm($formName) {
		if (!in_array($formName, $this->forms)) {
			$this->forms[] = $formName;
		}
		if (!class_exists($formName)) {
			ephFrame::loadClass('app.lib.component.Form.'.$formName);
		}
		$this->{$formName} = new $formName();
		$this->{$formName}->init($this);
		if ($this->action !== 'index') {
			$this->{$formName}->startup($this)->configure();
		}
		logg(Log::VERBOSE_SILENT, 'ephFrame: '.get_class($this).' loaded form '.$formName.'');
		return $this;
	}
	
	/**
	 * Provide named variable for view
	 *
	 * Use this method to set variables that should be available in the view
	 * when it’s rendered:
	 * <code>
	 * $this->set('UserName', $User->get('username');
	 * </code>
	 * Then you can access the variable like this:
	 * <code>
	 * Username: <?= $UserName; ?> (<a href="/logout/">logout</a>)
	 * </code>
	 * 
	 * Variables that you’ve allready set to a value will be overwritten, you
	 * can use {@link append} to append to variables.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Controller
	 */
	public function set($name, $value) {
		$this->data[$name] = $value;
		return $this;
	}
	
	/**
	 * Sets an other action for this controller, this affects the view that
	 * is used and also calls the method that has the same name as the action
	 * @param string $action
	 * @param array(mixed) $params
	 * @return boolean
	 */
	public function action($action, Array $params = array()) {
		assert(is_string($action) && !empty($action));
		logg(Log::VERBOSE, 'ephFrame: '.get_class($this).' changed action from \''.$this->action.'\' to \''.$action.'\'');
		$this->action = $action;
		$this->set('action', $this->action);
		$this->params = $params;
		// check params for special keys like layout, theme etc
		foreach (array('layout', 'theme') as $v) {
			if (isset($this->params[$v])) $this->{$v} = $this->params[$v];
		}
		if (method_exists($this, $action)) {
			// call beforeaction on every component
			foreach($this->components as $componentName) {
				$this->{$componentName}->beforeAction($action);
			}
			foreach($this->helpers as $helperName) {
				$className = ClassPath::className($helperName);
				$this->data->get($className)->beforeAction($action);
			}
			// call controller before[ActionName] if possible
			if (method_exists($this,'before'.ucFirst($action))) {
				$this->callMethod('before'.ucFirst($action));
			}
			// call beforeAction on every form
			foreach($this->forms as $FormName) {
				$this->{$FormName}->beforeAction();
			}
			if ($this->beforeAction() === false) {
				$this->name = 'error';
				$this->action('404', array());
			} else {
				$arguments = array_diff_key($params, array('controller' => 0, 'action' => 0, 'path' => 0));
				if ($this->callMethod($action, $arguments) === false) {
					$this->name = 'error';
					$this->action('404', array());
				}
			}
			// call controller after[ActionName] if possible
			if (method_exists($this,'after'.ucFirst($action))) {
				$this->callMethod('after'.ucFirst($action));
			}
			// call genereal afterAction
			$this->afterAction();
			// call afteraction on components
			foreach($this->components as $componentName) {
				$this->{$componentName}->afterAction($action);
			}
			foreach($this->helpers as $helperName) {
				$className = ClassPath::className($helperName);
				$this->data->get($className)->afterAction($action);
			}
		}
		return true;
	}
	
	/**
	 * Disables browser cache for this controller by sending not caching
	 * header commands to the client
	 * // todo set the headers in the response object instead of sending directly
	 * @return boolean true
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
	 * The Rendering action of the controller happens after the action was
	 * called.
	 * 
	 * @return string
	 */
	public function render() {
		// call beforeRender on Controller and Components
		if (!$this->beforeRender()) return false;
		foreach($this->components as $componentName) {
			$className = ClassPath::className($componentName);
			$this->{$className}->beforeRender($this);
		}
		foreach($this->helpers as $helperName) {
			$className = ClassPath::className($helperName);
			$this->data->get($className)->beforeRender($this);
		}
		// load view class if available
		if (!strpos($this->viewClassName, '.')) {
			ephFrame::loadClass('ephFrame.lib.'.$this->viewClassName);
		} else {
			ephFrame::loadClass($this->viewClassName);
		}
		// render the view part
		$view = new $this->viewClassName($this->name, $this->action, $this->data);
		$view->theme = $this->theme;
		if (!$this->response->header->isEmpty('Content-Type')) {
			$view->contentType = $this->response->header->get('Content-Type');
		}
		$content = $view->render();
		// wrap layout around view
		if (!empty($this->layout)) {
			$this->data->set('content', $content);
			$layout = new $this->viewClassName('layout', $this->layout, $this->data->toArray());
			$layout->theme = $this->theme;
			$layout->contentType = $view->contentType;
			$content = $layout->render();
		}
		// send content to each component
		foreach($this->components as $componentName) {
			$className = ClassPath::className($componentName);
			$content = $this->{$className}->afterRender($content);
		}
		foreach($this->helpers as $helperName) {
			$className = ClassPath::className($helperName);
			$this->data->get($className)->afterRender($content);
		}
		$this->response->body = $this->afterRender($content);
		// @todo add this to request/response
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && !preg_match('/gzip/i', $_SERVER['HTTP_ACCEPT_ENCODING']) && $this->response->enableGZipCompression) {
			$this->response->enableGZipCompression = false;
		}
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
	public function beforeRender() {
		return true;
	}
	
	/**
	 * Called everytime a controller gets a new action. called before the action
	 * is called and after all component are called.
	 * @param string $action action that is to be called
	 * @return boolean
	 */
	public function beforeAction() {
		return true;
	}
	
	/**
	 * Called after action is done
	 * @param string $action action that is done
	 * @return boolean
	 */
	public function afterAction() {
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
	public function afterRender($content) {
		// if we're in debugging mode we add the sql history dump to the view
		// content (this can be overwritten in the AppController.
		if (Registry::get('DEBUG') >= DEBUG_VERBOSE && $this->viewClassName == 'HTMLView') {
			$compileTime = ephFrame::compileTime(6);
			$debugOutput = 'Compile Time: '.$compileTime.'s ';
			if (class_exists('QueryHistory')) {
				$queryTime = QueryHistory::getInstance()->timeTotal(3);
				$queryCompilePercent = round($queryTime / $compileTime * 100);
				$debugOutput .= '('.$queryTime.'s/'.$queryCompilePercent.'% querytime, '.QueryHistory::getInstance()->count().' queries)';
			}
			$debugOutput .= LF.'Memory Usage: '.ephFrame::memoryUsage(true).' ('.ephFrame::memoryUsage().' Bytes)'.LF.LF;
			if (class_exists('QueryHistory')) {
				$debugOutput .= '<div class="queryHistory">'.QueryHistory::getInstance()->render().'</div>';
			}
			if ($this->viewClassName == 'HTMLView') {
				$content .= '<pre class="debugOutput">'.nl2br($debugOutput).'</pre>';	
			} else {
				$content .= $debugOutput;
			}
		}
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
	 * $this->redirect('http://code.nomoresleep.net/', 'p', true);
	 * </code>
	 * 
	 * @param string $url
	 * @param integer $status HTTP 1.1 status code
	 * @param boolean $exit exit after redirect
	 * @return boolean
	 */
	public function redirect($url, $status = 'p', $exit = true) {
		$this->beforeRedirect($url, $status, $exit);
		foreach($this->components as $componentName) {
			$className = ClassPath::className($componentName);
			$this->{$className}->beforeRedirect($url, $status, $exit);
		}
		if (!class_exists('HTTPStatusCode')) ephFrame::loadClass('ephFrame.lib.HTTPStatusCode');
		if ($url !== null) {
			header('Location: '.$url, true);
		}
		if (!empty($status)) {
			if (in_array($status, array('p', 'permanent', 'perm'))) $status = 301;
			if (in_array($status, array('t', 'tmp', 'temporary'))) $status = 307;
			if (isset(HTTPStatusCode::$statusCodes[$status])) {
				header(sprintf('HTTP/1.1 %s %s;', $status, HTTPStatusCode::$statusCodes[$status]), true);	
			}
		}
		if ($exit) {
			exit();
		}
		return true;
	}
	
	public function beforeRedirect($url, $status = 'p', $exit = true) {}
	
	/**
	 * Returns the refererrer submitted by the client if found
	 * Set $local to true to only use internal urls (external urls will be
	 * ignored)
	 * @param string $default default referer returned on empty referers
	 * @param boolean $local Use only local referers
	 * @return string
	 */
	public function referer($default = false, $local = true) {
		if ($this->request->referer) {
			// return refere only if in local domain
			if (!$local) {
				return $this->request->referer;
			} elseif (preg_match('/^(http:\/\/|)'.str_replace('.', '\.', $_SERVER['HTTP_HOST']).'/', $this->request->referer)) {
				return $this->request->referer;
			}
		}
		return $default;
	}

}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ControllerException extends BasicException {
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
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