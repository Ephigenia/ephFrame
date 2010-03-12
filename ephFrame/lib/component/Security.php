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
 * @modifiedby		$LastChangedBy: moresleep.net $
 * @lastmodified	$Date: 2009-07-09 20:58:41 +0200 (Thu, 09 Jul 2009) $
 * @filesource		$HeadURL: svn+ssh://moresleep.net/home/51916/data/ephFrame/0.2/ephFrame/lib/component/JSPacker.php $
 */

/**
 * Security Component
 *
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2009-07-22
 */
class Security extends AppComponent 
{
	/**
	 * List of controller actions that need to be posted
	 * @var array(string)
	 */
	public $requirePost = array();
	
	public function requirePost($actions) 
	{
		if (is_array($actions)) { 
			$this->requirePost = $actions;
		} else {
			$this->requirePost[] = $actions;
		}
		return $this;
	}
	
	public function beforeAction() 
	{
		if (!empty($requirePost) &&
			!$this->controller->request->isPost() &&
			($this->requirePost == 'all' || is_array($this->requirePost) && in_array($this->controller->action, $this->requirePost)
			) {
			return false;
		}
		return parent::beforeAction();
	}
}