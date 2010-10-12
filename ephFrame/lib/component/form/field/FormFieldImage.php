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

class_exists('FormFieldFile') or require dirname(__FILE__).'/FormFieldFile.php';

/**
 * Image Upload Form Field
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.component.form.Field
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 04.11.2008
 */
class FormFieldImage extends FormFieldFile 
{
	public $fileClassName = 'ephFrame.lib.file.image.Image';
}