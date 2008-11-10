<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright 2007+, Ephigenia M. Eichner, Kopernikusstr. 8, 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 	@license http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright Copyright 2007+, Ephigenia M. Eichner
 * 	@link http://code.ephigenia.de/projects/ephFrame/
 * 	@filesource
 */

require_once dirname(__FILE__).'/FormFieldText.php';

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component.Form.Field
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 04.11.2008
 */
class FormFieldCheckbox extends FormField {
	
	public $type = 'checkbox';
	
	public function checked($bool) {
		if ($bool) {
			$this->attributes->checked = 'checked';
		} else {
			$this->attributes->delete('checked');
		}
		return $this;
	}
	
	public function value($value = null) {
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

?>