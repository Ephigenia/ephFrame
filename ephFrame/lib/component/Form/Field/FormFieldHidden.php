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

class_exists('FormField') or require(dirname(__FILE__).'/FormField.php');

/**
 * Hidden Form field
 * 	
 * This class represents a type=hidden form input field.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 21.04.2009
 * @package ephFrame
 * @subpackage ephFrame.lib.component.Form.Field
 */
class FormFieldHidden extends FormField 
{	
	public $type = 'hidden';	
}