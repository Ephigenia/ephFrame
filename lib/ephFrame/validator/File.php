<?php

namespace ephFrame\validator;

class File extends Validator
{
	public $maxSize;
	
	public $minSize;
	
	public $mimeTypes = array();

	public $messages = array(
		'NOT_FOUND' => 'The file could not be found',
		'MAXSIZE' => 'The file is too large (:size). Allowed maximum size is :maxSize',
		'MINSIZE' => 'The file is too small (:size). Allowed minimum size is :minSize',
		'MIMETYPE' => 'The mime typ of the file is invalid (:mimeType). Allowed mimetypes are :types',
		'NOT_READABLE' => 'The file is not readable.',
		UPLOAD_ERR_INI_SIZE => 'The file is to large. Please change the "upload_max_filesize" setting in the php.ini',
		UPLOAD_ERR_FORM_SIZE => 'UPLOAD_ERR_FORM_SIZE',
		UPLOAD_ERR_PARTIAL => 'The file was partially uploaded',
		UPLOAD_ERR_NO_TMP_DIR => 'Could not upload a file because a temporary directory is missing (UPLOAD_ERR_NO_TMP_DIR)',
		UPLOAD_ERR_CANT_WRITE => 'Could not write file to disk (UPLOAD_ERR_CANT_WRITE)',
		UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload (UPLOAD_ERR_EXTENSION)',
	);
	
	public function __construct(Array $options = array())
	{
		if ($iniSize = ini_get('upload_max_filesize')) {
			if (preg_match('@^(\d+)M$i@', $iniSize, $found)) {
				$this->maxSize = $found[1] * 1024 * 1204;
			} elseif (preg_match('@^(\d+)K$i@', $iniSize, $found)) {
				$this->maxSize = $found[1] * 1024;
			}
		}
		return parent::__construct($options);
	}
	
	public function validateSize(\ephFrame\File\UploadedFile $File)
	{
		if (isset($this->maxSize) && $File->size() > $this->maxSize) {
			$this->size = $File->size();
			$this->message = $this->messages['MAXSIZE'];
			return false;
		}
		if (isset($this->minSize) && $File->size() < $this->minSize) {
			$this->size = $File->size();
			$this->message = $this->messages['MINSIZE'];
			return false;
		}
		return true;
	}
	
	public function validateMimeType(\ephFrame\File\UploadedFile $File)
	{
		if (!empty($this->mimeTypes) && !in_array($File->mimeType(), $this->mimeTypes)) {
			$this->mimeType = $File->mimeType();
			$this->types = implode(', ', $this->mimeTypes);
			$this->message = $this->messages['MIMETYPE'];
			return false;
		}
		return true;
	}
	
	public function validateUploadError(\ephFrame\File\UploadedFile $File)
	{
		switch($File->error) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
			case UPLOAD_ERR_NO_TMP_DIR:
			case UPLOAD_ERR_PARTIAL:
            case UPLOAD_ERR_CANT_WRITE:
            case UPLOAD_ERR_EXTENSION:
				$this->message = $this->messages[$File->error];
				return false;
				break;
		}
		return true;
	}
	
	public function validate($File)
	{
		if (is_array($File) && isset($file['tmp_name'])) {
			$File = new \ephFrame\File\File((string) $file['tmp_name']);
		}
		if (!$File instanceof \ephFrame\File\File) {
			$File = new \ephFrame\File\File((string) $file);
		}
		if ($File instanceof \ephFrame\File\UploadedFile && !$this->validateUploadError($File)) {
			return false;
		}
		if (!$File->exists()) {
			$this->message = $this->messages['NOT_FOUND'];
			return false;
		}
		if (!$File->readable()) {
			$this->message = $this->messages['NOT_READABLE'];
			return false;
		}
		if (!$this->validateSize($File)) {
			return false;
		}
		if (!$this->validateMimeType($File)) {
			return false;
		}
		return true;
	}
}