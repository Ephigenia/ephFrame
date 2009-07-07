<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright (c) 2007+, Ephigenia M. Eichner
 * 						 Kopernikusstr. 8
 * 						 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 
 * 	@license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright		copyright 2007+, Ephigenia M. Eichner
 * 	@link			http://code.ephigenia.de/projects/ephFrame/
 * 	@version		$Revision$
 * 	@modifiedby		$LastChangedBy$
 * 	@lastmodified	$Date$
 * 	@filesource		$HeadURL$
 */

class_exists('Router') or require dirname(__FILE__).'/Router.php';
class_exists('HTTPRequest') or require dirname(__FILE__).'/HTTPRequest.php';

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
			$request = new HTTPRequest(true);
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
				return $this->dispatch('Error/MissingController', array('controllerName' => $controllerName));
			}
		}
		$controllerName = ephFrame::loadClass($controllerClassPath);
		if (!class_exists($controllerName)) {
			return $this->dispatch('Error/MissingController', array('controllerName' => $controllerName));
		}
		try {
			$controller = new $controllerName($request);
			$controller->action($router->action, $router->params);
			echo $controller->render();
		} catch (ViewFileNotFoundException $e) {
			return $this->dispatch('Error/MissingView', array('missingController' => $router->controller, 'missingAction' => $router->action));
		// missing database tables
		} catch (MySQLTableNotFoundException $e) {
			if ($router->controller !== 'Error' && $router->action !== 'MissingTable') {
				return $this->dispatch('Error/MissingTable', array('tablename' => $e->tablename));
			} else {
				throw $e;
			}
		// missing Databases
		} catch (MySQLDBNotFoundException $e) {
			die('Missing Database <q>'.$e->databaseName.'</q>');
		// failed DB Connection 
		} catch (MySQLConnectionAccessDeniedException $e) {
			die('check db connection string, invalid login');
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