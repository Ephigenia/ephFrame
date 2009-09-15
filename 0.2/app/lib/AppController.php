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
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright	copyright 2007+, Ephigenia M. Eichner
 * @link		http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @filesource
 */

// load parent class
ephFrame::loadClass('ephFrame.lib.Controller');

/**
 * Applications Main Controller
 * 
 * The AppController is your main controller in the application. Every
 * controller in this application should be extended by this one. By this
 * you can add main methods, variables, components and helpers that should
 * be available by every controller in the application.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 10.08.2007
 * @package app
 * @subpackage app.lib
 */
class AppController extends Controller
{	
	public $helpers = array(
		'HTML'
	);
	
	public $components = array(
		'CSS',
		'JavaScript',
		'MetaTags',
		'Email'
	);
	
	public function beforeRender() {
		$this->CSS->addFiles(array(
			'reset',
			'app',
			'form',
		));
		if (Registry::get('DEBUG') > DEBUG_PRODUCTION) {
			$this->CSS->addFile('debug');
		}
		return parent::beforeRender();
	}
	
	public function testEmail() {
		// $this->Email->delivery = 'debug';
		$this->Email->from = 'ephigenia@mac.com';
		$this->Email->subject = 'TESTMAIL';
		$this->Email->attach('/Users/Ephigenia/Sites/session_fixation.pdf');
		$this->Email->attach('testfilename.txt', 'an other textfile');
//		$this->Email->htmlMessage = '<strong>HTML CONTENT</strong>alskjd';
		$this->Email->message = 'NOCH EINE NACHRICHT '.LF.'ü ä ö Ü Ä Ö ß – …';
		$r = $this->Email->send($this->Email->from, 'ü ä ö Ü Ä Ö ß – …');
		var_dump($r);
		die();
	}
	
} // END AppController class

/**
 * @package app
 * @subpackage app.libs.exceptions
 */
class AppControllerException extends ControllerException
{}