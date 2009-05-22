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

// load needed classes
class_exists('Hash') or require dirname(__FILE__).'/Hash.php';

/**
 * 	A Router
 * 
 * 	The Router acts like a dispatcher that matches the incoming request to
 * 	a mvc structure by returning the controller that fits to the incoming
 * 	request.<br />
 * 	<br />
 * 	parse request url strings to get controller and action names
 * 
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 18.09.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@version 0.1
 */
class Router extends Hash {
	
	/**
	 * 	Default dispatching rules
	 * 	@var array(string)
	 */
	public $data = array(
		'root' => array(
			'controller' => 'app',
			'action' => 'index'
		)
	);
	
	/**
	 * 	Stores parameter values matched to a route
	 * 	@var array(string)
	 */
	public $params = array();
	
	/**
	 * 	Default Controller Name
	 * 	@var string
	 */
	public $controller = 'app';
	
	/**
	 *  Default controller action name
	 * 	@var string
	 */
	public $action = 'index';
	
	/**
	 * 	@return Router
	 */
	public function __construct() {
		$this->loadRoutes();
		return $this;
	}
	
	
	public static $instance;
	
	/**
	 * 	@return DBConnectionManager
	 */
	public static function getInstance() {
		if (empty(self::$instance)) {
			self::$instance = new Router();
		}
		return self::$instance;
	}
	
	/**
	 * 	Load the routes into the {@link data} array
	 */
	private function loadRoutes() {
		require_once APP_ROOT.'config'.DS.'routes.php';
	}
	
	/**
	 *	Parses a url to extract controller, action and params if specified
	 * 	in router data array.
	 * 	@param string $url
	 * 	@return Router
	 */
	public function parse($url) {
		$debug = false;
		assert(is_string($url) || empty($url));
		$routeMatch = false;
		Log::write(LOG::VERBOSE, get_class($this).': finding route for \''.$url.'\'');
		// add some default scaffolding routes
		Router::addRoute('scaffold_view', ':controller/(?P<id>\d+)/?', array('action' => 'view'));
		Router::addRoute('scaffold_actions', ':controller/(?P<id>\d+)/:action/?');
		Router::addRoute('scaffold_create', ':controller/:action/?');
		Router::addRoute('scaffold_search', ':controller/search/:searchTerm', array('action' => 'search'));
		Router::addRoute('scaffold_controller', ':controller');
		// only set root route if not existent allready
		if (!Router::getInstance()->hasKey('root')) {
			Router::addRoute('root', '/');
		}
		// go through routes and try to find a matching route
		foreach(self::getInstance() as $routeName => $routeData) {
			if (!isset($routeData['path'])) continue;
			$routeTemplate = $routeData['path'];
			$paramRegExp = $this->createRouteRegexp($routeTemplate);
			if ($debug) {
				echo 'url: '.$url.'<br />';
				echo 'pth: '.$routeData['path'].'<br />';
				echo 'tpl: '.$routeTemplate.'<br />';
				echo 'reg: '.htmlentities($paramRegExp).'<br />';
			}
			if (preg_match_all($paramRegExp, $url, $match)) {
				$this->params = array_merge($this->params, $routeData);
				// extract controller and action if found
				if (isset($match['controller'])) {
					$this->controller = $match['controller'][0];
				} elseif (isset($this->params['controller'])) {
					$this->controller = $this->params['controller'];
				}
				if (isset($match['action'])) {
					$this->action = $match['action'][0];
				} elseif (isset($this->params['action'])) {
					$this->action = $this->params['action'];
				}
				// extract other parameter names
				foreach($match as $key => $value) {
					if (!preg_match('/[0-9]+/', $key)) {
						if ($key == 'id') {
							$value[0] = (int) $value[0];
						}
						$this->params[$key] = $value[0];
					}
				}
				// merge with params coming from param array
				$routeMatch = true;
				if ($debug) echo 'MATCH!<br />';
				Log::write(Log::VERBOSE, get_class($this).': result is: '.$this->controller.'Controller->'.$this->action);
				break;
			}
			if ($debug) echo '<br />';
		}
		if (!$routeMatch && Registry::get('DEBUG') < DEBUG_DEVELOPMENT) {
			$this->controller = 'Error';
			$this->action = '404';
		}
		//$debug = 1;
		if ($debug) {
			echo '<br /><strong>result:</strong><br />';
			echo 'controller: '.$this->controller.'<br />';
			echo 'action: '.$this->action.'<br />';
			echo 'params: '.var_export($this->params, true).'<br />';
			exit;
		}
		return $this;
	}
	
	/**
	 *	Translates a route template to a valid regular expression and returns it
	 *	
	 * 	@param string	$routeTemplate
	 * 	@return string
	 */
	private function createRouteRegexp($routeTemplate) {
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
		$regexp = preg_replace('@:([^:\/]+)@', '(?P<\\1>[^\/:]+)', $regexp);
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
	 *	Return a uri route named $routeName
	 *
	 *	This can be very helpfull to fullfill DRY principles. This will return
	 *	the uri to a named route you’ve defined in the router before using 
	 *	{@link addRoute}.
	 *
	 *	<code>
	 *	echo $HTML->link(Router::getRoute('login'), 'login');
	 *	</code>
	 *
	 *	This will also replace some parameters you defined in the uri (not
	 *	implemented yet!!!)
	 *	<code>
	 *	echo $HTML->link(Router::getRoute('userEdit', array('id' => $User->id)), 'User editieren');
	 *	</code>
	 *	
	 * 	@param string			$routeName	name of route that should be returned
	 * 	@param array(string)	$params		array of parameters to replace in the uri of the route if foudn
	 * 	@param boolean 		$includeWebroot
	 * 	@return string|boolean 	false if route name could not be found, otherwise the resulting uri
	 */
	public static function getRoute($routeName, $params = array(), $includeWebroot = true) {
		$router = self::getInstance();
		if (!$router->hasKey($routeName)) {
			return false;
		}
		if (!($routeConfig = $router->get($routeName)) || !isset($routeConfig['path'])) {
			return false;
		}
		$uri = $routeConfig['path'];
		// replace params
		if (is_array($params)) {
			$uri = self::insertParams($uri, $params);
		}
		if ($includeWebroot) {
			$uri = WEBROOT.$uri;
		}
		return $uri;
	}
	
	/**
	 *	Replaces all parameter placeholders (:id or :username) in an uri-string
	 *	with the values from the second parameter array
	 * 	@param string			$uri
	 * 	@param array(string)	$params
	 * 	@return string
	 */
	public static function insertParams($uri, Array $params = array())  {
		foreach($params as $k => $v) {
			$uri = str_replace(':'.$k, $v, $uri);
		}
		return $uri;	
	}
	
	/**
	 *	Add route to routes list
	 * 	
	 * 	@param string $routeName	name of that route
	 * 	@param string $path uri for the route, including param regexps
	 * 	@param array(string) $params default resulting parameters
	 */
	public static function addRoute($routeName = null, $path, Array $params = array()) {
		$router = self::getInstance();
		// strip / from path
		$path = ltrim($path, '/');
		// route names that are added after they are allready there become a copy
		// of the original router if their params are empty
		if ($router->hasKey($routeName) && empty($params) && func_num_args() >= 3) {
			$params = $router->get($routeName);
			$params['path'] = $path;
			$router->add($routeName.'_copy_'.rand(), $params);
		} else {
			$params['path'] = $path;
			if ($routeName == null) {
				$router->add($params);
			} else {
				$router->add($routeName, $params);
			}
		}
		return true;
	}
	
}

?>