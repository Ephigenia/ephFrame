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
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

// load parent class
class_exists('View') or require dirname(__FILE__).'/View.php';

/**
 * A view that is a HTML Page
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @version 0.1
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 10.08.2007
 */
class HTMLView extends View {
	
	/**
	 * Content type for this view that can be send to the client
	 * @var string
	 */
	public $contentType = 'text/html';
		
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class HTMLViewException extends ViewException {}