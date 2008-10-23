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

// load parent class
ephFrame::loadClass('ephFrame.lib.Controller');

/**
 * 	Applications Main Controller
 * 	
 * 	The AppController is your main controller in the application. Every
 * 	controller in this application should be extended by this one. By this
 * 	you can add main methods, variables, components and helpers that should
 * 	be available by every controller in the application.
 * 	
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 10.08.2007
 * 	@package app
 * 	@subpackage app.lib
 */
class AppController extends Controller {
	
	public $helpers = array(
		'HTML'
	);
	
	public $components = array(
		'CSS',
		'JavaScript',
		'MetaTags'
	);
	
	public function beforeRender() {
		$this->CSS->addFile('reset', 'core', 'app');
		return parent::beforeRender();
	}
	
}

/**
 * 	@package app
 * 	@subpackage app.libs.exceptions
 */
class AppControllerException extends ControllerException {}

?>