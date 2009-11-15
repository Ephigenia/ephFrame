<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
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
class ErrorController extends AppController
{	
	public $viewClassName = 'HTMLView';
	
	public function beforeRender()
	{
		if (Registry::get('DEBUG') < DEBUG_DEVELOPMENT) {
			$this->action('404');
			$this->set('url', $this->request->uri);
			$this->response->header->statusCode = 404;
		}
		return parent::beforeRender();
	}
	
	public function directoryNotWritable()
	{
		die(var_dump($this->params));
	}
	
	public function missingController($controller)
	{
		$this->set('controllerName', coalesce($controller, 'unknown'));
	}
	
	public function themeNotFound($theme)
	{
		$this->theme = false;
		$this->set('theme', $theme);
	}
	
	public function missingLayoutFile($filename, $layout)
	{
		$this->set('layout', $layout);
		$this->set('filename', $filename);
		$this->layout = 'default';
	}
	
	public function missingView($filename) {
		$this->set('filename', $filename);
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