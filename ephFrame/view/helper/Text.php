<?php

namespace ephFrame\view\helper;

class Text extends \ephFrame\view\Helper
{
	public function autoURL($text, $attributes = '')
	{
		if (!empty($attributes)) {
			$attributes = ' '.trim((string) $attributes);
		}
		return preg_replace('@(?<!href="|">|src=")((?:http|https|ftp|nntp)://[^ <]+)@i', '<a href="\1"'.$attributes.'>\1</a>', $text);
	}
	
	public function autoEmail($text, $attributes = '')
	{
		if (!empty($attributes)) {
			$attributes = ' '.trim((string) $attributes);
		}
		return preg_replace(
			'/([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})/im',
			'<a href="mailto:\1"'.$attributes.'>\1</a>',
			$text
		);
	}
}