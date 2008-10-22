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
		),
		'scaffold_actions' => array(
			'path' => ':controller/(?P<id>\d+)/:action/'
		),
		'scaffold_view' => array(
			'path' => ':controller/(?P<id>\d+)/',
			'action' => 'view'
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
		// go through routes and try to find a matching route
		foreach($this as $routeName => $routeData) {
			if (!empty($routeData['path'])) {
				$routeTemplate = $routeData['path'];
				$paramRegExp = $this->createRouteRegexp($routeTemplate);
				if ($debug) {
					echo 'url: '.$url.'<br />';
					echo 'tpl: '.$routeTemplate.'<br />';
					echo 'reg: '.htmlentities($paramRegExp).'<br />';
				}
				if (preg_match_all($paramRegExp, $url, $match)) {
					// extract controller and action if found
					if (isset($match['controller'])) {
						$this->controller = $match['controller'][0];
					} elseif (isset($routeData['controller'])) {
						$this->controller = $routeData['controller'];
					}
					if (isset($match['action'])) {
						$this->action = $match['action'][0];
					} elseif (isset($routeData['action'])) {
						$this->action = $routeData['action'];
					}
					// extract other parameter names
					foreach($match as $key => $value) {
						if (!preg_match('/[0-9]+/', $key)) {
							$this->params[$key] = $value[0];
						}
					}
					// merge with params coming from param array
					$routeMatch = true;
					if ($debug) echo 'MATCH!<br />';
					break;
				}
				if ($debug) echo '<br />';
			}
		}
		if ($debug) {
			echo '<br /><strong>result:</strong><br />';
			echo 'controller: '.$this->controller.'<br />';
			echo 'action: '.$this->action.'<br />';
			echo 'params: '.var_dump($this->params).'<br />';
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
		$parameterRegexp = preg_replace('/:([^:\/]+)/', '(?P<\\1>[^\/:]+)', $routeTemplate);
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
	
	private function addRoute($routeName = null, $path, $params = null) {
		$routeData = array('path' => $path);
		if (isset($params['controller'])) {
			$routeData['controller'] = $params['controller'];
			unset($params['controller']); 
		}
		if (isset($params['action'])) {
			$routeData['action'] = $params['action'];
			unset($params['action']); 
		}
		$this->add($routeName, $routeData);
	}
	
}

?>