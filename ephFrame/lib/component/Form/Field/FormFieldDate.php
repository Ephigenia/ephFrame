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

class_exists('FormFieldText') or require(dirname(__FILE__).'/FormFieldText.php');

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.component.Form.Field
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 20.02.2009
 */
class FormFieldDate extends FormFieldText {
	
	public $format = 'd.m.Y';
	
	public function afterConstruct() {
		$this->value(date($this->format));
		return parent::afterConstruct();
	}
	
	public function value($value = null) {
		if (func_num_args() == 0) {
			// translate passed date to timestamp
			return strtotime(parent::value());
		}
		$value = trim($value);
		if (empty($value)) {
			return parent::value('');
		}
		// convert timestamps to date in form
		if (preg_match('@^\d+$@', $value)) {
			$value = date($this->format, $value);
		}
		return parent::value($value);
	}
	
}