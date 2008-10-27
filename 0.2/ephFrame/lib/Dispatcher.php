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

require FRAME_LIB_DIR.'Router.php';
require FRAME_LIB_DIR.'HTTPRequest.php'; 
//ephFrame::loadClass('ephFrame.lib.Router');
//ephFrame::loadClass('ephFrame.lib.HTTPRequest');

/**
 * 	Application Router / Controller / action dispatcher
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de
 * 	@since 02.12.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class Dispatcher extends Object {
	
	/**
	 *	Dispatches a controller class, depending on the passed {@link HTTPRequest}
	 * 	Object or given url
	 * 
	 * 	@param string|HTTPRequest $requestObjectOrUrl
	 * 	@return Controller
	 */
	public function dispatch($requestObjectOrUrl = null, Array $params = array()) {
		// use original request
		if (is_object($requestObjectOrUrl)) {
			assert($requestObjectOrUrl instanceof HTTPRequest);
			$request = $requestObjectOrUrl;
		// fake the request
		} elseif (is_string($requestObjectOrUrl)) {
			$request = new HTTPRequest(false);
			$request->data['__url'] = $requestObjectOrUrl;
		}
		$router = new Router();
		$router->params = $params;
		$router->parse($request->get('__url'));
		// load controller and construct it
		$controllerName = ucFirst($router->controller).'Controller';
		if ($controllerName == 'AppController') {
			$controllerClassPath = 'app.lib.'.$controllerName;
		} else {
			$controllerClassPath = 'app.lib.controller.'.$controllerName;
		}
		// if controller class not found dispatch controller not found action
		if (!ClassPath::exists($controllerClassPath)) {
			if ($controllerName == 'ErrorController') {
				$controllerClassPath = 'ephFrame.lib.ErrorController';
			} else {
				$params = array('controllerName' => $controllerName);
				return $this->dispatch('Error/ControllerNotFound', $params);
			}
		}	
		ephFrame::loadClass($controllerClassPath);
		try {
			$controller = new $controllerName($request, $router->params);
			$controller->action($router->action, $router->params);
			echo $controller->render();
		} catch (ViewFileNotFoundException $e) {
			return $this->dispatch('Error/MissingView', array('controller' => $router->controller, 'action' => $router->action));
		} catch (MySQLTableNotFoundException $e) {
			if ($router->controller !== 'Error' && $router->action !== 'MissingTable') {
				return $this->dispatch('Error/MissingTable', array('tablename' => $e->tablename));
			} else {
				throw $e;
			}
		}
		return $controller;
	}
	
}

/**
 *  Basic Dispatcher Exception
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class DispatcherException extends BasicException {}

/**
 *	Exception that is thrown on invalid dispatching parameters
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class DispatcherInvalidParamsException extends DispatcherException {}

?>