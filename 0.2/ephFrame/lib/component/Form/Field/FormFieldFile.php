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

class_exists('FormField') or require(dirname(__FILE__).'/FormField.php');
class_exists('File') or require(dirname(__FILE__).'/../../../File.php');
class_exists('PHPINI') or require(dirname(__FILE__).'/../../../PHPINI.php');

/**
 * 	Simple Form File Upload Field
 * 
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
		if (func_num_args() == 0 && $this->isUploaded()) {
			$file = new $this->fileClassName($_FILES[$this->attributes->name]['tmp_name']);
			return $file;
		}
		return false;
	}
	
	public function validate($value = null) {
		// check for uploading errors
		if (func_num_args() == 0 && empty($this->error)) {
			switch(@$_FILES[$this->attributes->name]['error']) {
				case UPLOAD_ERR_OK:
					return true;
					break;
				case UPLOAD_ERR_INI_SIZE:
            		$this->error = 'The uploaded file is to large. Maximum file size is '. File::sizeHumanized(PHPINI::get('upload_max_filesize').'.');
        			break;
        		case UPLOAD_ERR_FORM_SIZE:
            		$this->error = 'File size exeeds form size defined in form.';
        			 break;
		        case UPLOAD_ERR_PARTIAL:
		        	$this->error = 'File Upload incomplete';
		            break;
		        case UPLOAD_ERR_NO_FILE:
		            $this->error = 'No File Uploaded';
					break;
		        case UPLOAD_ERR_NO_TMP_DIR:
		            $this->error = 'Temporary upload directory missing.';
		        	break;
		        case UPLOAD_ERR_CANT_WRITE:
		            $this->error = 'Error while writing uploaded file to disk.';
		        	break;
        		default:
        			$this->error = sprintf('An unknown error occured during or after the upload. Error code is: %s', @$_FILES[$this->attributes['name']]['error']);
            		break;
			}
		}
		return parent::validate($value);
	}
	
	/**
	 *	Checks if any upload has happened
	 * 	@return boolean
	 */
	protected function isUploaded() {
		if (!isset($this->form) || isset($this->form) && !$this->form->submitted()) {
			return false;
		}
		if (empty($_FILES[$this->attributes->name]['tmp_name'])) {
			return false;
		}
		return true;
	}
	
	/**
	 *	Returns the name of the file uploaded if any file was uploaded.
	 * 	@return string
	 */
	public function originalFilename() {
		if (!$this->isUploaded()) return false;
		return $_FILES[$this->attributes->name]['name'];
	}
	
}

/**
 * 	File form field exception
 *	@author Ephigenia // Marcel Eichner <love@ephigenia.de>
 *	@since 26.05.2009
 */
class FormFieldFileException extends FormFieldException {
	
}

?>