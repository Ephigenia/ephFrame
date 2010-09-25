<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @modifiedby		$LastChangedBy: moresleep.net $
 * @lastmodified	$Date: 2009-08-05 14:01:22 +0200 (Wed, 05 Aug 2009) $
 * @filesource		$HeadURL: svn+ssh://moresleep.net/home/51916/data/ephFrame/0.2/ephFrame/lib/helper/String.php $
 */

class_exists('Helper') or require dirname(__FILE__).'/Helper.php';
class_exists('Validator') or require dirname(__FILE__).'/../util/Validator.php';

/**
 * Text Helper
 *
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2009-08-13
 * @version 0.1
 * @uses Validator
 */
class Text extends Helper 
{
	/**
	 * Other Helpers used by this Helper
	 * @var array(string)
	 */
	public $helpers = array(
		'HTML',
	);
	
	/**
	 * Trims a $text till the <!--more--> marks like in wordpress and replaces
	 * it with the optional $label and using $title as link title
	 * 
	 * <code>
	 * echo $Text->more($BlogPost->text, $BlogPost->detailPageUri(), 'more …');
	 * </code>
	 * 
	 * @param string $text
	 * @param string $url link targeting url
	 * @param string $label
	 * @param string $title
	 * @return string Returns the resulting string
	 */
	public function more($text, $url, $label = false, $title = null)
	{
		$regexp = '@<!--(.+)-->.*@is';
		if (preg_match($regexp, $text, $found)) {
			if (!empty($found[1]) && !in_array(strtolower($found[1]), array('more', 'mehr'))) {
				$label = $found[1];
			}
			$replace = $this->HTML->link($url, $label, array('title' => $title, 'class' => 'more'));
			$text = preg_replace($regexp, $replace, $text);
		}
		return $text;
	}
	
	/**
	 * Return the first $count sentences from a $text
	 * 
	 * @param string $text
	 * @param integer $count
	 * @return string
	 */
	public static function excerpt($text, $count = 1)
	{
		$sentenceCount = preg_match_all('@[^0-9][.!?]{1,}\s*@', $text, $found, PREG_OFFSET_CAPTURE);
		if ($count > $sentenceCount) {
			return $text;
		}
		$excerpt = substr($text, 0, $found[0][$count-1][1] + 2);
		return String::closeTags($excerpt);
	}
	
	/**
	 * Wrap $word in $text into a passed tag
	 * 
	 * Example highlighting a search query string in a website
	 * <code>
	 * 	echo $Text->highlight($BlogPost->text, $query, '<em class="q">$1</em>');
	 * </code>
	 * 
	 * @param string $text
	 * @param string $keyword
	 * @return string
	 */
	public static function highlight($text, $keyword, $replace = '<em class="keyword">$1</em>')
	{
		$text = preg_replace('@('.preg_quote($keyword, '@').'(?!([^<]+)?>))@i', $replace, $text);
		return $text;
	}
	
	/**
	 * Replace URLs with HTML-Tags
	 * 
	 * Replaces all found urls (http/https/ftp/nntp) with an html link tag. You
	 * can pass additional attributes of the tag with $attributes.
	 * 
	 * @param string $text
	 * @param array(string) $attributes key=value pairs for attributes
	 * @return string
	 */
	public static function autoURLs($text, $attributes = '')
	{
		$text = preg_replace('@(?<!href="|">|src=")((?:http|https|ftp|nntp)://[^ <]+)@i', '<a href="\1" rel="external" '.$attributes.'>\1</a>', $text);
		return $text;
	}
	
	/**
	 * Convert email adresses that are in $text into html-links
	 * @param string $text
	 * @param array(string) $attributes additional attributes for the a-tag
	 * @return string
	 */
	public static function autoEmail($text, $attributes = '')
	{
		return preg_replace(Validator::EMAIL, '<a href="mailto:\1" '.$attributes.'>\1</a>', $text);
	}	
}