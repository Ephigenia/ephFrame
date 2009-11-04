<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision: 241 $
 * @modifiedby		$LastChangedBy: moresleep.net $
 * @lastmodified	$Date: 2009-08-05 14:01:22 +0200 (Wed, 05 Aug 2009) $
 * @filesource		$HeadURL: svn+ssh://moresleep.net/home/51916/data/ephFrame/0.2/ephFrame/lib/helper/String.php $
 */

class_exists('Helper') or require dirname(__FILE__).'/Helper.php';
class_exists('Validator') or require dirname(__FILE__).'/Validator.php';

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
	 * Replace urls in a string with html links
	 * @param string $text
	 * @param array(string) $attributes
	 * @return string
	 */
	public static function autoURLs($text, $attributes = '')
	{
		$text = preg_replace('@(?<!href="|">)((?:http|https|ftp|nntp)://[^ <]+)@i', '<a href="\1" rel="external" '.$attributes.'>\1</a>', $text);
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