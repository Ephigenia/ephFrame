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

class_exists('FormFieldFile') or require dirname(__FILE__).'/FormFieldFile.php';
class_exists('Image') or require dirname(__FILE__).'/../../../Image.php';

/**
 * Image Upload Form Field
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.component.Form.Field
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 04.11.2008
 */
class FormFieldImage extends FormFieldFile {
	
	public $fileClassName = 'Image';
	
}