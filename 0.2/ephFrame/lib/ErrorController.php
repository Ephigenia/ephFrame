<?php

require_once APP_LIB_DIR.'AppController.php';

/**
 * 	Error Controller
 * 	
 * 	This controller is automatically called on any exception that happens
 * 	in the application that is not catched. See the index.php file in the
 * 	webroot folder.
 * 
 * 	@package app
 * 	@subpackage app.lib.controller
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 02.11.2007
 */
class ErrorController extends AppController {
	
	public $viewClassName = 'HTMLView';
	
	public function beforeRender() {
		if (Registry::get('DEBUG') < DEBUG_DEVELOPMENT) {
			$this->action('404');
			$this->set('url', $this->request->uri);
			$this->response->header->statusCode = 404;
		}
		return parent::beforeRender();
	}
	
	public function controllerNotFound($params) {
		if (isset($params['controllerName'])) {
			$this->set('missingControllerName', $params['controllerName']);
		} else {
			$this->set('missingControllerName', '[unknownName]');
		}
	}
	
	public function missingView($params) {
		$this->set('missingController', $params['controller']);
		$this->set('missingAction', $params['action']);
	}
	
	public function missingTable($params) {
		$this->set('tablename', $params['tablename']);
	}

	/**
	 *	
	 */
	public function index() {
		/*
		$exception = $this->data->exception;
		if (Registry::get('DEBUG') != DEBUG_PRODUCTION) {
			$this->action = 'exception';
			// add custom exception pages here
			switch (get_class($exception)) {
				
			} 
		}*/
	}
	
}

?>