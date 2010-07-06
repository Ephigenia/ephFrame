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

class_exists('FormFieldText') or require(dirname(__FILE__).'/FormFieldNumber.php');

/**
 * Form Field for float values
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.component.Form.Field
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2010-03-25
 */
class FormFieldFloat extends FormFieldNumber 
{
	/**
	 * Default validation rules for emails, should be emails
	 * @var array(string)
	 */
	public $validate = array(
		'valid' => array(
			'regexp' => Validator::FLOAT,
			'message' => 'The value you’ve entered seemes not to be a valid float value.',
		)
	);
}