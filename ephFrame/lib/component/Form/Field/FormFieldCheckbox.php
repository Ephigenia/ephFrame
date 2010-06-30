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

class_exists('FormField') or require(dirname(__FILE__).'/FormField.php');

/**
 * Single checkbox form field class
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.component.Form.Field
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 04.11.2008
 */
class FormFieldCheckbox extends FormField 
{
	public $mandatory = false;
	
	public $type = 'checkbox';
	
	public function checked($bool) 
	{
		if ($bool) {
			$this->attributes->checked = 'checked';
		} else {
			$this->attributes->delete('checked');
		}
		return $this;
	}
	
	public function check() 
	{
		return $this->checked(true);
	}
	
	public function uncheck() 
	{
		return $this->checked(false);
	}
	
	public function value($value = null) 
	{
		if (func_num_args() == 0) {
			if ($this->form->submitted() && !empty($this->form->request->data[$this->attributes->name])) {
				if (!empty($this->attributes->value)) {
					return $this->attributes->value;
				}
				return true;
			}
			return false;
		} else {
			$this->attributes->value = $value;
		}
		return $this;
	}	
}