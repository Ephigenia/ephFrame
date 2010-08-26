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
 * @filesource
 */

class_exists('SGMLTag') or require dirname(__FILE__).'/SGMLTag.php';

/**
 * A HTML Tag
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 01.07.2007
 * @version 0.1
 */
class HTMLTag extends SGMLTag 
{	
	public $noShortTags = array('script', 'textarea', 'a');
	
	/**
	 * Switch this to off to generate HTML Valid tags, important for tags
	 * with no content
	 */
	public static $XHTML = true;

	public function renderOpenTag() 
	{
		if (empty($this->tagName)) return '';
		$rendered = $this->tagIndent().self::OPEN.$this->tagName;
		if (count($this->attributes) > 0) $rendered .= ' '.$this->attributes->render();
		if (empty($this->tagValue) && !$this->hasChildren() && strcasecmp($this->tagName, 'script') !== 0
			&& !in_array($this->tagName, $this->noShortTags) && HTMLTag::$XHTML) $rendered .= ' /';
		$rendered .= self::CLOSE;
		return $rendered;
	}
	
	public function renderCloseTag() 
	{
		foreach($this->noShortTags as $tagName) {
			if (strcasecmp($this->tagName, $tagName) !== 0) continue;
			return self::OPEN.'/'.$tagName.self::CLOSE;
		}
		if (!empty($this->tagValue) || $this->hasChildren()) {
			$rendered = '';
			if ($this->hasChildren()) {
				$rendered .= LF;
			}
			$rendered .= $this->tagIndent().self::OPEN.'/'.$this->tagName.self::CLOSE;
			if ($this->hasChildren()) {
				$rendered = LF.$this->tagIndent().$rendered;
			}
			return $rendered;
		}
		return '';
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 */
class HTMLTagException extends SGMLTagException 
{}