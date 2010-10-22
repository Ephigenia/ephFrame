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

class_exists('FormFieldText') or require(dirname(__FILE__).'/FormFieldText.php');

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.component.form.Field
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 20.02.2009
 */
class FormFieldDate extends FormFieldText
{
	// public $type = 'date';
	
	/**
	 * Date format used by strftime function. Depends on the currently locale
	 * settings. Check {@link I18n} for more information.
	 * @var string
	 */
	public $format = '%m/%d/%Y';
	
	public function afterConstruct() 
	{
		// default value should be the current date
		if ($this->attributes->value === null) {
			$this->value(time());
		}
		return parent::afterConstruct();
	}
	
	public function value($value = null) 
	{
		if (func_num_args() == 0) {
			return strtotime(parent::value());
		}
		if (preg_match('@^\d+$@', $value)) {
			$value = strftime($this->format, $value);
		}
		return parent::value($value);
	}
}