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
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright	copyright 2007+, Ephigenia M. Eichner
 * @link		http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

/**
 * This is the file which stores all url routes to the correct controllers and
 * actions including possible parameters.
 * 
 * @package app
 * @subpackage app.config
 */
Router::addRoute('root', 	'/',		array('controller' => 'App', 'action' => 'index'));
Router::addRoute(null,		'/:action',	array('controller' => 'App', 'action' => 'index'));