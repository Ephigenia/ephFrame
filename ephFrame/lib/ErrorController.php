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

/**
 * Error Controller
 * 
 * @package app
 * @subpackage app.lib.controller
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 02.11.2007
 */
class ErrorController extends AppController
{
	public $layout = 'default';
	
	public $components = array(
		'CSS',
	);
	
	public function beforeRender()
	{
		// always display 404 page in production mode
		if (empty($this->params['status']) && Registry::get('DEBUG') <= DEBUG_PRODUCTION) {
			$this->error(404);
		}
		return parent::beforeRender();
	}
	
	public function error($statusCode, $message = null)
	{
		$this->response->header->statusCode = (int) $statusCode;
		$this->data->set('url', $this->request->uri);
		$this->action = 'error'.$statusCode;
	}
	
	public function directoryNotWritable()
	{
		$this->data->set('dir', $this->params['directory']);
	}
	
	public function missingController($controller)
	{
		$this->data->set('controllerName', coalesce($controller, 'unknown'));
	}
	
	public function themeNotFound($theme)
	{
		$this->theme = false;
		$this->data->set('theme', $theme);
	}
	
	public function missingLayoutFile($filename, $layout)
	{
		$this->data->set('layout', $layout);
		$this->data->set('filename', $filename);
		$this->layout = 'default';
	}
	
	public function missingView($filename) 
	{
		$this->data->set('filename', $filename);
	}
	
	public function missingTable() 
	{
		$this->data->set('tablename', $this->params['tablename']);
	}
	
	public function missingDB() 
	{
		$this->data->set('databaseName', $this->params['databaseName']);
	}
}