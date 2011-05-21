<?php

namespace ephFrame\storage;

class Cache extends Adaptable
{
	public static $options = array(
		'adapter' => 'ephFrame\storage\cache\File',
	);
}