<?php

namespace ephFrame\view\helper;

class HTML extends \ephFrame\view\Helper
{
	public function tag($name, $content, Array $attributes = array())
	{
		return new \ephFrame\HTML\Tag((string) $name, $content, $attributes);
	}
	
	public function email($email, $label = null, Array $attributes = array())
	{
		$attributes['href'] = 'mailto:'.$email;
		if ($label && !isset($attributes['title'])) {
			$attributes['title'] = strip_tags($label);
		}
		return $this->tag('a', $label ?: $email, $attributes);
	}
	
	public function link($url, $label = null, Array $attributes = array())
	{
		if (!isset($attributes['href'])) {
			$attributes['href'] = $url;
		}
		if (!empty($label)) {
			if (!isset($attributes['title'])) {
				$attributes['title'] = strip_tags($label);
			}
		} else {
			$label = $url;
		}
		return $this->tag('a', $label, $attributes);
	}
	
	public function image($src, Array $attributes = array())
	{
		return $this->tag('img', null, array_merge($attributes, array('src' => $src)));
	}
	
	public function __call($tagName, Array $arguments = array())
	{
		array_unshift($arguments, $tagName);
		return call_user_func_array(array($this, 'tag'), $arguments);
	}
}