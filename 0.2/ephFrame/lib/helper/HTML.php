<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright 2007+, Ephigenia M. Eichner, Kopernikusstr. 8, 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 	@license http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright Copyright 2007+, Ephigenia M. Eichner
 * 	@link http://code.ephigenia.de/projects/ephFrame/
 * 	@filesource
 */

ephFrame::loadClass('ephFrame.lib.HTMLTag');

/**
 * 	HTML Helper
 * 
 * 	View Helper Class for quick echoeing html tags. This can be make the rendering
 * 	of simple html tags much easier.
 * 	
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de
 *  @since 01.01.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.helper
 * 	@uses HTMLTag
 */
class HTML extends Helper {
	
	/**
	 * 	Creates a new HTML Tag and returns it
	 * 	@param string $tagName
	 * 	@param string $value
	 * 	@param array $attributes
	 * 	@return HTMLTag
	 */
	private function createTag($tagName, Array $attributes = array()) {
		return new HTMLTag($tagName, $attributes);
	}
	
	/**
	 *	Creates a simple <p> element with $content and $attributes ans returns
	 * 	it ready for rendering.
	 * 	
	 * 	<code>
	 * 	// in a view you can use this just like that:
	 * 	echo $HTML->p('hello I\'m your P!', array('class' => 'hint'));
	 * 	</code>
	 * 	
	 * 	@param string $content
	 * 	@param array(string) $attributes
	 * 	@return HTMLTag
	 */
	public function p($content, Array $attributes = array()) {
		return new HTMLTag('p', $attributes, $content);
	}
	
	/**
	 * 	Creates a XHTML Valid link element.
	 * 	@param string $url
	 * 	@param array(string) $attributes
	 * 	@return HTMLTag
	 */
	public function link($url, $label, Array $attributes = array()) {
		if (!empty($url)) {
			$attributes['href'] = $url;
		}
		if (!empty($label) && !isset($attributes['title'])) {
			$attributes['title'] = htmlentities($label);
		}
		$tag = $this->createTag('a', $attributes);
		if (is_object($label)) {
			$tag->addChild($label);
		} else {
			$tag->tagValue = $label;
		}
		return $tag;
	}
	
	/**
	 * 	Returns a Image HTML Tag
	 * 	@param string $src
	 * 	@param array(string) $attributes
	 * 	@return HTMLTag
	 */
	public function image($src, Array $attributes = array()) {
		$attributes['src'] = $src;
		$tag = $this->createTag('img', $attributes);
		return $tag;
	}
	
	/**
	 * 	Alias for {@link image}
	 * 	@return string
	 */
	public function img() {
		$args = func_get_args();
		return $this->callMethod('image', $args);
	}
	
}

?>