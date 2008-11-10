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

require_once dirname(__FILE__).'/FormField.php';
require_once dirname(__FILE__).'/../../../File.php';

/**
 * 	Simple Form File Upload Field	
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 04.11.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component.Form.Field
 */
class FormFieldFile extends FormField {
	
	/**
	 *	Name of the class that should be returned by {@link value}
	 * 	@var string
	 */
	public $fileClassName = 'File';
	
	/**
	 *	Overwrite Parents type attribute
	 * 	@var string
	 */
	public $type = 'file';
	
	/**
	 *	Form Field File Values can be just read, not written and will return
	 * 	an instance of $fileClassName class if field was submitted with a file
	 * 	@return File.
	 */
	public function value($value = null) {
		if (func_num_args() == 0) {
			if (!isset($this->form) || isset($this->form) && !$this->form->submitted()) {
				return false;
			}
			if (empty($_FILES[$this->attributes->name]['tmp_name'])) {
				return false;
			}
			$file = new $this->fileClassName($_FILES[$this->attributes->name]['tmp_name']);
			return $file;
		}
	}
	
}

?>