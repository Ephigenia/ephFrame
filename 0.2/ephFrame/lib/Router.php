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
ephFrame::loadClass('ephFrame.lib.Hash');

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
		Router::addRoute('scaffold_actions', ':controller/(?P<id>\d+)/:action/');
		Router::addRoute('scaffold_view', ':controller/(?P<id>\d+)/', array('action' => 'view'));
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
				$this->params = $routeData;
				// extract controller and action if found
				if (isset($this->params['controller'])) {
					$this->controller = $this->params['controller'];
				} elseif (isset($match['controller'])) {
					$this->controller = $match['controller'][0];
				}
				if (isset($this->params['action'])) {
					$this->action = $this->params['action'];
				} elseif (isset($match['action'])) {
					$this->action = $match['action'][0];
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
		if ($debug) {
			echo '<br /><strong>result:</strong><br />';
			echo 'controller: '.$this->controller.'<br />';
			echo 'action: '.$this->action.'<br />';
			echo 'params: '.var_dump($router->params).'<br />';
			exit;
		}
		// no matching route found, find route default way
		// otherwise try to parse the default way
		if (!$routeMatch) {
			$this->parseDefaultAction($url);
		}
		return $this;
	}
	
	private function createRouteRegexp($routeTemplate) {
		if (substr($routeTemplate, -2, 2) == '/*') {
			$routeTemplate = substr($routeTemplate, 0, -2).'/.*';
		}
		// first replace :id with integer matching
		$parameterRegexp = preg_replace('/:id/', '(?P<id>\d+)', $routeTemplate);
		// then replac all other :varname placeholders with real regexp patterns
		$parameterRegexp = preg_replace('/:([^:\/]+)/', '(?P<\\1>[^\/:]+)', $parameterRegexp);
		$regexp = '{^';
		if (substr($routeTemplate, -1, 1) == '/') {
			$regexp .= $parameterRegexp;
		} else {
			$regexp .= $parameterRegexp.'/?';
		}
		$regexp .= '$}i';
		return $regexp;
	}
	
	private function parseDefaultAction($url = null) {
		preg_match('!^/?(?P<controller>[^\/]+)\/?(?P<action>[^\/]+)?/?!', $url, $found);
		// do the controller boogie ...
		if (isset($found['controller'])) {
			$this->controller = $found['controller'];
		} else {
			$this->controller = $this->data['root']['controller'];
		}
		// ... the action way
		if (isset($found['action'])) {
			$this->action = $found['action'];
		} else {
			$this->action = $this->data['root']['action'];	
		}
		return $this;
	}
	
	public static function getRoute($routeName) {
		$router = self::getInstance();
		if ($router->hasKey($routeName)) {
			$route = $router->get($routeName);
			return $route['path'];
		}
		return false;
	}
	
	public static function addRoute($routeName = null, $path, Array $params = array()) {
		$router = self::getInstance();
		// strip / from path
		$path = ltrim($path, '/');
		// route names that are added after they are allready there become a copy
		// of the original router if their params are empty
		if ($router->hasKey($routeName) && empty($params)) {
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