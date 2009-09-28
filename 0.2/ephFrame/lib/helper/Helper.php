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

/**
 * Abstract Helper Class
 * 
 * A Helper is sometimes a singleton class or simpler classes.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 19.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 */
abstract class Helper extends Object {

	protected $controller;

	public function __construct($controller = null) {
		if (is_object($controller))	$this->controller = $controller;
		return $this;
	}
	
}