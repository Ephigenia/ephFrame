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

ephFrame::loadClass('ephFrame.lib.HTMLTag');

/**
 * HTML Helper
 * 
 * View Helper Class for quick echoeing html tags. This can be make the rendering
 * of simple html tags much easier.
 * 
 * See the examples for every method of this helper to get an overview. Also
 * you can enhance the possibilities of this helper by chanhing your
 * {@link AppHelper} class.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 01.01.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 * @uses HTMLTag
 */
class HTML extends AppHelper 
{	
	/**
	 * Create valid {@link HTMLTag} object with passed arguments and return it
	 * 
	 * <code>
	 * // create a a-tag with a link to a homepage
	 * echo $HTML->tag('a', 'The Developer’s Homepage', array('href' => 'http://www.marceleichner.de'));
	 * // create simple p-tags with content
	 * echo $HTML->tag('p', 'this is a message to everybody!');
	 * </code>
	 * 
	 * @param string $tagName
	 * @param string $value
	 * @param array $attributes
	 * @return HTMLTag
	 */
	public function tag($tagName, $content = null, Array $attributes = array()) 
	{
		return new HTMLTag($tagName, $attributes, $content);
	}
	
	/**
	 * This is basically the same like {@link tag} but it will only return the
	 * opening tag for the created tag not the the closing tag.
	 * @param $tagName
	 * @param $attributes
	 * @return String
	 */
	public function openTag($tagName, Array $attributes = array()) 
	{
		$tag = new HTMLTag($tagName, $attributes, '&nbsp;');
		return $tag->renderOpenTag();
	}
	
	/**
	 * Creates a simple <p> element with $content and $attributes ans returns
	 * it ready for rendering.
	 * 
	 * <code>
	 * // in a view you can use this just like that:
	 * echo $HTML->p('hello I\'m your P!', array('class' => 'hint'));
	 * </code>
	 * 
	 * @param string $content
	 * @param array(string) $attributes
	 * @return HTMLTag
	 */
	public function p($content, Array $attributes = array()) 
	{
		return $this->tag('p', $content, $attributes);
	}
	
	/**
	 * Returns a {@link HTMLTag} object with a link to an email addy.
	 * 
	 * The email addy will be encoded so some spam-bots will not be able to
	 * recognize it’s a email addy. Also you can add an optional alternate 
	 * $label:
	 * <code>
	 * // will create a link to mailto:love@ephigenia.de
	 * echo $HTML->email('love@ephigenia.de');
	 * // use alternate label
	 * echo $HTML->email('love@ephigenia.de', 'ephFrame Author');
	 * </code>
	 * 
	 * @param string email
	 * @param string label optional alternate label, default is the email addy
	 * @param array(string) $attributes optional additional attributes for {@link HTMLTag}
	 * @return string
	 */
	public function email($email, $label = null, Array $attributes = array()) 
	{
		$emailEncoded = String::htmlOrdEncode($email);
		if ($label == null) {
			$label = $emailEncoded;
		}
		return $this->link('mailto:'.$emailEncoded, $label, $attributes);
	}
	
	/**
	 * Returns an A-Tag as {@link HTMLTag} object
	 * 
	 * The title attribute for the link is automatically created if not passed
	 * in the $attributes array.
	 * 
	 * @param string $url
	 * @param string $label optional alternate label, default is the url
	 * @param array(string) $attributes optional link attributes
	 * @return HTMLTag
	 */
	public function link($url, $label = null, Array $attributes = array()) 
	{
		if (!empty($url)) {
			$attributes['href'] = $url;
		}
		if (!empty($label) && !isset($attributes['title'])) { //} && !preg_match('/<[^>]+>/', $label)) {
			$attributes['title'] = strip_tags($label);
		}
		if ($label === null || $label === false || $label === '') {
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
	 * Return a IMG-Tag as {@link HTMLTag} object.
	 * 
	 * This will also use the theme as searchpath for the passed image url to
	 * look for an image.
	 * 
	 * @param string $src source url for the image that should be used
	 * @param array(string) $attributes optional additional attributes for img-tag
	 * @return HTMLTag
	 */
	public function image($src, Array $attributes = array()) 
	{
		if ($src == false) {
			return false;
		}
		if (strpos($src, '/') !== 0 && substr($src, 0, 7) !== 'http://') {
			$searchPaths[] = $src;
			if ($this->controller instanceof Controller && $this->controller->theme) {
				$searchPaths[] = STATIC_DIR.'theme/'.$this->controller->theme.'/img/'.$src;
			}
			$searchPaths[] = STATIC_DIR.'img/'.$src;
			$src = WEBROOT.$searchPaths[count($searchPaths)-1];
			foreach($searchPaths as $filename) {
				if (!is_file($filename)) continue;
				$src = WEBROOT.$filename;
				break;
			}
		}
		$attributes['src'] = $src;
		if (empty($attributes['alt']) && @$attributes['alt'] !== false) {
			$attributes['alt'] = '';
			if (!empty($attributes['title'])) {
				$attributes['alt'] = $attributes['title'];
			}
		}
		if (empty($attributes['title']) && @$attributes['title'] !== false && !empty($attributes['alt'])) {
			$attributes['title'] = $attributes['alt'];
		}
		$tag = $this->tag('img', null, $attributes);
		return $tag;
	}
	
	/**
	 * Alias for {@link image}
	 * @return string
	 */
	public function img() 
	{
		$args = func_get_args();
		return $this->callMethod('image', $args);
	}
}