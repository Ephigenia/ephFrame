<?php

namespace ephFrame\HTML\Form\Element;

class File extends Element
{
	public $attributes = array(
		'type' => 'file',
	);
	
	public $uploadedFileClassname = 'ephFrame\File\UploadedFile';
	
	public function defaultValidators()
	{
		return array(
			'file' => new \ephFrame\Validator\File(),
		);
	}
	
	public function defaultFilters()
	{
		return array();
	}
	
	public function submit($data)
	{
		if (is_array($data) && $data['error'] != UPLOAD_ERR_NO_FILE) {
			return parent::submit(
				new $this->uploadedFileClassname($data)
			);
		}
		return true;
	}
}