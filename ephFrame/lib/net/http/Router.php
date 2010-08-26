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

// load needed classes
class_exists('Hash') or require dirname(__FILE__).'/../../core/Hash.php';

/**
 * A Router
 * 
 * The Router acts like a dispatcher that matches the incoming request to
 * a mvc structure by returning the controller that fits to the incoming
 * request.<br />
 * <br />
 * parse request url strings to get controller and action names
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.09.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @version 0.1
 */
class Router extends Hash
{	
	/**
	 * Default dispatching rules
	 * @var array(string)
	 */
	public $data = array(
		'root' => array(
			'controller' => 'app',
			'action' => 'index'
		)
	);
	
	/**
	 * Stores parameter values matched to a route
	 * @var array(string)
	 */
	public $params = array();
	
	/**
	 * Default Controller Name
	 * @var string
	 */
	public $controller = 'app';
	
	/**
	 * Default controller action name
	 * @var string
	 */
	public $action = 'index';
	
	/**
	 * @return Router
	 */
	public function __construct() 
	{
		$this->loadRoutes();
		return $this;
	}
	
	static private $instance = null;
	
	/**
	 * @return DBConnectionManager
	 */
	public static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new Router();
		}
		return self::$instance;
	}
	
	private function __clone() {}
	
	/**
	 * Load the routes into the {@link data} array
	 */
	protected function loadRoutes()
	{
		require_once APP_ROOT.'config'.DS.'routes.php';
	}
	
	public function addScaffoldRoutes() 
	{
		// add some default scaffolding routes
		Router::addRoute('scaffold_view', ':controller/(?P<id>\d+)', array('action' => 'view'));
		Router::addRoute('scaffold_actions', ':controller/(?P<id>\d+)/:action');
		Router::addRoute('scaffold_paged', ':controller/page-:page');
		Router::addRoute('scaffold', ':controller/:action', array('action' => 'index'));
		Router::addRoute('scaffold_search', ':controller/search/:q', array('action' => 'search'));
		Router::addRoute('scaffold_controller', ':controller');
		// only set root route if not existent allready
		if (!Router::instance()->hasKey('root')) {
			Router::addRoute('root', '/');
		}
		return true;
	}
	
	/**
	 * Parses a url to extract controller, action and params if specified
	 * in router data array.
	 * @param string $url
	 * @return Router
	 */
	public function parse($url)
	{
		$url = (string) $url;
		Log::write(LOG::VERBOSE, get_class($this).': parsing \''.$url.'\'');
		$this->addScaffoldRoutes(); // @todo move this somewhere else
		// go through routes and try to find a matching route
		foreach(self::instance() as $routeName => $routeData) {
			if (!preg_match_all($this->createRouteRegexp(@$routeData['path']), $url, $match, PREG_SET_ORDER)) {
				continue;
			}
			$this->params = array_merge($this->params, $routeData);
			if (isset($this->params['controllerPrefix'])) {
				$this->controller = $this->params['controllerPrefix'];
			} else {
				$this->controller = '';
			}
			if (isset($match[0]['controller'])) {
				$this->controller .= ucfirst($match[0]['controller']);
			} elseif (!empty($this->params['controller'])) {
				$this->controller .= $this->params['controller'];
			}
			if (!empty($match[0]['action'])) {
				$this->action = $match[0]['action'];
			} else {
				$this->action = @$this->params['action'];
			}
			// add controller action prefix
			if (!empty($routeData['prefix'])) {
				$this->action = $routeData['prefix'].ucFirst($this->action);
			}
			// extract other parameter names
			foreach($match[0] as $key => $value) {
				if (is_int($key)) continue;
				if (preg_match('/^\d+$/', $value)) {
					$value = (int) $value;
				}
				$this->params[$key] = $value;
			}
			break;
		}
		if (isset($match[0][0])) {
			Log::write(Log::VERBOSE, get_class($this).': result '.$this->controller.'Controller->'.$this->action);
		} else {
			Log::write(Log::VERBOSE, get_class($this).': no matching route found.');
			$this->controller = 'Error';
			$this->action = 'error';
			$this->params['status'] = 404;
		}
		return $this;
	}
	
	/**
	 * Translates a route template to a valid regular expression and returns it
	 * 
	 * @param string $routeTemplate
	 * @return string
	 */
	private function createRouteRegexp($routeTemplate)
	{
		$regexp = trim($routeTemplate);
		// return root uri for empty templates
		if ($regexp == '/' || $regexp == '') {
			return '@^/?$@';
		}
		// replace varnames
		$regexp = preg_replace('@:id@', '(?P<id>\d+)', $regexp);
		// replace typed :varname notation regexp
		$regexp = preg_replace('@:([^:\/]+)_int@', '(?P<\\1>\d+)', $regexp);
		// replace :varname notation with regexp named match
		$regexp = preg_replace('@:([^:\/]+)@', '(?P<\\1>[^\/:]+)?', $regexp);
		// routes with /* at the end match everything else
		if (substr($regexp, -2) == '/*') {
			$regexp = substr($regexp, 0, -2).'/.*';
		// add trailing optional slash
		} elseif (substr($regexp, -2) != '/?') {
			$regexp .= '/?';
		}
		return '@^'.$regexp.'$@i';
	}
	
	/**
	 * Return a uri route named $routeName
	 *
	 * This can be very helpfull to fullfill DRY principles. This will return
	 * the uri to a named route you’ve defined in the router before using 
	 * {@link addRoute}.
	 *
	 * <code>
	 * echo $HTML->link(Router::getRoute('login'), 'login');
	 * </code>
	 *
	 * This will also replace some parameters you defined in the uri (not
	 * implemented yet!!!)
	 * <code>
	 * echo $HTML->link(Router::getRoute('userEdit', array('id' => $User->id)), 'User editieren');
	 * </code>
	 * 
	 * @param string $routeName	name of route that should be returned
	 * @param array(string)	$params	array of parameters to replace in the uri of the route if foudn
	 * @param boolean $permanent return permanent url or just the uri part
	 * @return string|boolean 	false if route name could not be found, otherwise the resulting uri
	 */
	public static function getRoute($routeName = null, $params = array(), $permanent = false)
	{
		$router = self::instance();
		// get current uri
		if ($routeName == null) {
			$uri = coalesce(@$_REQUEST['__url'], false);
		} elseif (!$router->hasKey($routeName)) {
			return false;
		} else {
			$routeConfig = $router->get($routeName);
			$uri = $routeConfig['path'];
			// parameter replacement
			if (is_array($params)) {
				$uriReg = preg_replace('@\(\?P<([^>]+)>[^)]+\)@', ':$1', $uri);
				$uri = String::substitute($uriReg, array_merge($routeConfig, $params));
			}
		}
		if ($permanent) {
			$uri = rtrim(Registry::get('WEBROOT_URL'), '/').WEBROOT.$uri;
		} else {
			$uri = WEBROOT.$uri;
		}
		return $uri;
	}
	
	public static function url($routeName = null, $params = array())
	{
		return self::getRoute($routeName, $params, true);
	}
	
	public static function uri($routeName = null, $params = array())
	{
		return self::getRoute($routeName, $params, false);
	}
	
	/**
	 * Add route to routes list
	 * 
	 * @param string $routeName	name of that route
	 * @param string $path uri for the route, including param regexps
	 * @param array(string) $params default resulting parameters
	 */
	public static function addRoute($routeName = null, $path, Array $params = array())
	{
		$router = self::instance();
		// default route parameters
		$default = array(
			'path' => ltrim($path, '/'),
			'controllerPrefix' => false,
			'prefix' => false,
			'action' => 'index',
			'controller' => 'app',
		);
		// route names that are added after they are allready there become a copy
		// of the original router if their params are empty
		if ($router->hasKey($routeName) && empty($params) && func_num_args() >= 3) {
			$params = $router->get($routeName);
			$router->add($routeName.'_copy_'.rand(), array_merge($default, $params));
		} else {
			$params = array_merge($default, $params);
			if ($routeName == null) {
				$router->add($params);
			} else {
				$router->add($routeName, $params);
			}
		}
		return true;
	}	
}