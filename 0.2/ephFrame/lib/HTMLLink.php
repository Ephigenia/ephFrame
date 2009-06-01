<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright (c) 2007+, Ephigenia M. Eichner
 * 						 Kopernikusstr. 8
 * 						 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 
 * 	@license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright		copyright 2007+, Ephigenia M. Eichner
 * 	@link			http://code.ephigenia.de/projects/ephFrame/
 * 	@version		$Revision$
 * 	@modifiedby		$LastChangedBy$
 * 	@lastmodified	$Date$
 * 	@filesource		$HeadURL$
 */

// load parent class
class_exists('HTMLTag') or require dirname(__FILE__).'/HTMLTag.php';

/**
 *	A Class for HTML Links, just like the 'a'-tag in HTML
 * 
 * 	This class is very usefull in web projects where you have a lot of links.
 * 	Use this for link lists, sub tags, arrays of links and so on. Overwrite
 * 	every method!
 * 
 * 	This will echo a html valid link sourcecode:
 * 	<code>
 * 	$link = new HTMLLink('http://www.ephigenia.de', 'Ephigenia', '_blank');
 * 	echo $link;
 * 	</code>
 * 
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 12.07.2007
 * 	@uses String
 */
class HTMLLink extends HTMLTag {
	
	/**
	 *	Creates a HTMLLink
	 * 	@param string $url
	 * 	@param string $label
	 * 	@param string $target
	 * 	@return HTMLLink
	 */
	public function __construct($url, $label = null, $target = null) {
		parent::__construct('a', array('href' => $url, 'target' => $target), $label);
		return $this;
	}
	
	/**
	 * 	Sets the links target or return it
	 *	@param string $target
	 * 	@return string
	 */
	public function target($target = null) {
		assert(is_string($target));
		return $this->attribute('target', $target);
	}
	
	/**
	 * 	Sets the url to link to or return it. This can also be any
	 * 	type of object that is inherited by {@link URL} (the url is rendered)
	 * 	@param URL|string $href
	 * 	@return string|HTMLLink
	 */
	public function href($href = null) {
		if (func_num_args() > 0 && get_parent_class($href) == 'URL') {
			$href = $href->render();
		}
		return $this->attribute('href', $href);
	}
	
	/**
	 *	Alias for {@link href}
	 * 	@param string $url
	 * 	@return HTMLLink|string
	 */
	public function url($url = null) {
		return $this->href($url);
	}
	
	/**
	 * 	Sets or returns the link's value. This is basicly an alias for
	 * 	{@link nodeValue} of the parent class. The title is set right
	 * 	after you set the label.
	 * 	@param string $label
	 * 	@return HTMLLink|String 
	 */
	public function label($label = null) {
		if (func_num_args() == 1) {
			return $this->tagValue($label);	
		} else {
			$this->tagValue($label);
			$this->title($label);
		}
		return $this;
	}
	
	/**
	 *	Sets or returns the title of this Link
	 * 	@param string $title
	 * 	@return HTMLLink|string
	 */
	public function title($title = null) {
		// remove shit from title
		if (func_num_args() > 0) {
			assert(is_string($title) || is_integer($title) || is_float($title));
			$title = String::stripTags($title);
			$title = String::stripBrakes($tags);
		}
		return $this->attribute('title', $title);
	}
	
}

?>