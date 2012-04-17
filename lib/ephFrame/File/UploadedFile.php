<?php

namespace ephFrame\File;

class UploadedFile extends File
{
	public $originalFilename;
	
	public $error;

	public function __construct($filename, $originalFilename = null, $mimeType = null, $error = null)
	{
		$this->originalFilename = basename($originalFilename);
		$this->mimeType = $mimeType;
		$this->error = $error ?: UPLOAD_ERR_OK;
		return parent::__construct($filename);
	}
}