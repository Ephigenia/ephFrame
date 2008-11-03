<?php

require_once dirname(__FILE__).'/FormField.php';
require_once dirname(__FILE__).'/../../../File.php';

class FormFieldFile extends FormField {
	
	/**
	 *	Name of the class that should be returned by {@link value}
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
			if (isset($_FILES[$this->attributes->name])) {
				return new $this->fileClassName($_FILES[$this->attributes->name]['tmp_name']);
			}
		}
	}
	
}

?>