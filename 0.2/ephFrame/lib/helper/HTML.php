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
	private function tag($tagName, $content = null, Array $attributes = array()) {
		return new HTMLTag($tagName, $attributes, $content);
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
	 *	Creates an email link, but encoding the email address
	 * 	@param string email
	 * 	@param string label
	 * 	@return string
	 */
	public function email($email, $label = null) {
		$emailEncoded = String::htmlOrdEncode($email);
		if ($label == null) {
			$label = $emailEncoded;
		}
		return $this->link('mailto:'.$emailEncoded, $label);
	}
	
	/**
	 * 	Creates a XHTML Valid link element.
	 * 	@param string $url
	 * 	@param array(string) $attributes
	 * 	@return HTMLTag
	 */
	public function link($url, $label = null, Array $attributes = array()) {
		if (!empty($url)) {
			$attributes['href'] = $url;
		}
		if (!empty($label) && !isset($attributes['title']) && !preg_match('/<[^>]+>/', $label)) {
			$attributes['title'] = $label;
		}
		if (empty($label) && $label !== false) {
			$label = $url;
		}
		$tag = $this->tag('a', null, $attributes);
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
		if (empty($attributes['alt']) && (isset($attributes['alt']) && $attributes['alt'] !== false)) {
			$attributes['alt'] = '';
			if (!empty($attributes['title'])) {
				$attributes['alt'] = $attributes['title'];
			}
		}
		if (empty($attributes['title']) && (isset($attributes['title']) && $attributes['title'] !== false) && !empty($attributes['alt'])) {
			$attributes['title'] = $attributes['alt'];
		}
		$tag = $this->tag('img', null, $attributes);
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