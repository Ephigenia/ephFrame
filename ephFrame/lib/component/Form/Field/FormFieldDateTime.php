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
 * @filesource
 */

class_exists('FormFieldDate') or require(dirname(__FILE__).'/FormFieldDate.php');

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.component.Form.Field
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 20.02.2009
 */
class FormFieldDateTime extends FormFieldDate 
{
	/**
	 * Override parents date/time format with hours and seconds on it
	 * @var string
	 */
	public $format = '%x %H:%M';
}