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

class_exists('FormFieldText') or require dirname(__FILE__).'/FormFieldText.php';

/**
 * 	URL-Input Field
 * 	
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component.Form.Field
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 04.11.2008
 */
class FormFieldUrl extends FormFieldText {

	/**
	 *	Default validation rules for urls
	 * 	@var array(string)
	 */
	public $validate = array(
		'valid' => array(
			'regexp' => Validator::URL,
			'message' => 'The URL you’ve entered is not valid.'
		)
	);
	
	public function value($value = null) {
		if (func_num_args() == 0) {
			$val = parent::value();
			if (!empty($val) && substr($val, 0, 7) !== 'http://') {
				$val = 'http://'.$val;
			}
			return $val;
		}
		return parent::value($value);
	}
	
}

?>