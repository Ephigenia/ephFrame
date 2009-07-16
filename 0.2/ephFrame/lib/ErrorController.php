<?php

/**
 * ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

class_exists('AppController') or require APP_LIB_DIR.'AppController.php';

/**
 * Error Controller
 * 
 * This controller is automatically called on any exception that happens
 * in the application that is not catched. See the index.php file in the
 * webroot folder.
 * 
 * @package app
 * @subpackage app.lib.controller
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 02.11.2007
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
	
	public function missingController() {
		if (isset($this->params['controllerName'])) {
			$this->set('controllerName', $this->params['controllerName']);
		} else {
			$this->set('controllerName', '[unknownName]');
		}
	}
	
	public function missingLayoutFile() {
		$filename = $this->params['missingLayoutFilename'];
		$basename = basename($filename);
		$layoutname = substr($basename, 0, -strrpos($basename,'.'));
		$this->set('layoutname', $layoutname);
		$this->set('filename', $filename);
	}
	
	public function missingView() {
		$this->set('missingController', $this->params['missingController']);
		$this->set('missingAction', $this->params['missingAction']);
	}
	
	public function missingTable() {
		$this->set('tablename', $this->params['tablename']);
	}
	
	public function missingDB() {
		$this->set('databaseName', $this->params['databaseName']);
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