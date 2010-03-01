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

class_exists('HTMLView') or require dirname(__FILE__).'/HTMLView.php';

/**
 * Simple implementation for xml views, sending the correct content type
 * headers.
 * 
 * To use this view simply change the viewClassname in the controller:
 * <code>
 * $this->vieClassname = 'XMLView';
 * </code>
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib	
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 22.10.2008
 */
class XMLView extends HTMLView {
	
	public $contentType = 'application/rss+xml';
	
}