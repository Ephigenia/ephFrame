<?php

namespace ephFrame\storage;

class Cache extends Adaptable
{
	protected static $options = array(
		'adapter' => 'ephFrame\storage\cache\File',
	);
}