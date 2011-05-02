<?php

namespace ephFrame\view\helper;

use \ephFrame\util\String;

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
	
	public static $moreRegexp = '@<!--(.+)-->.*@is';
	
	/**
	 * Trims a $text till the <!--more--> marks like in wordpress and replaces
	 * it with the optional $label and using $attributes for the genrated link
	 * 
	 * <code>
	 * echo $Text->more($BlogPost->text, $BlogPost->detailPageUri(), 'more …');
	 * </code>
	 * 
	 * @param string $text
	 * @param string $url link targeting url, or false when text just should be trimmed to more mark
	 * @param string $label default label that should be used
	 * @param array(string) $attributes
	 * @return string Returns the resulting string
	 */
	public static function more($text, $url = false, $label = false, Array $attributes = array())
	{
		$attributes += array(
			'title' => $label
		);
		$HTML = new \ephFrame\view\helper\HTML();
		if (preg_match(self::$moreRegexp, $text, $found)) {
			if (!empty($found[1]) && $found[1] !== 'more' || $label == false) {
				$label = $found[1];
			}
			$text = preg_replace(self::$moreRegexp, $HTML->link($url, $label, $attributes), $text);
		}
		return $text;
	}
	
	public static $excerptRegexp = '@[^0-9][.!?]{1,}\s*@';
	
	/**
	 * Return the first $count sentences from a $text
	 * 
	 * @param string $text
	 * @param integer $count
	 * @return string
	 */
	public static function excerpt($text, $count = 1)
	{
		$sentenceCount = preg_match_all(self::$excerptRegexp, $text, $found, PREG_OFFSET_CAPTURE);
		if ($count > $sentenceCount) {
			return $text;
		}
		$excerpt = substr($text, 0, $found[0][$count-1][1] + 2);
		return String::closeTags($excerpt);
	}
	
	/**
	 * Counts the number of paragraphs in a text, just counting line breaks
	 * is the simpler explaination
	 * @param string $string
	 * @return integer Number of paragraphs found
	 */
	public static function countParagraphs($string)
	{
		return substr_count($string, PHP_EOL);
	}
}