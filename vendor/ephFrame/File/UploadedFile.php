<?php

namespace ephFrame\File;

class UploadedFile extends File
{
	public $originalFilename;

	public function __construct(Array $data)
	{
		$this->originalFilename = $data['name'];
		$this->mimeType = $data['type'];
		$this->error = $data['error'];
		return parent::__construct($data['tmp_name']);
	}
	
	public function move($path, $createDirs = true)
	{
		if ($createDirs && !is_dir(dirname($path))) {
			mkdir(dirname($path), 0755, true);
		}
		if (!move_uploaded_file($this->path, $path)) {
			throw new Exception(sprintf('Could not move uploaded file %s to %s', $this->path, $path));
		}
		$this->path = $path;
		return $this;
	}
}