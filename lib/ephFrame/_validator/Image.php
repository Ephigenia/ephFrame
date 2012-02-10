<?php

namespace ephFrame\validator;

class Image extends File
{
	public $mimeTypes = array(
		'image/jpeg',
		'image/jpg',
		'image/png',
		'image/gif',
	);
}