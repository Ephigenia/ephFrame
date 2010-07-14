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
 * @lastmodified	$Date: 2009-08-21 13:24:05 +0200 (Fri, 21 Aug 2009) $
 * @filesource		$HeadURL: svn+ssh://moresleep.net/home/51916/data/ephFrame/0.2/ephFrame/lib/helper/Time.php $
 */

/**
 * Pagination Helper Class
 *
 * The Paginator Helper should help you showing pagination links in your
 * application.
 * 
 * @todo some examples would be nice to see here
 *
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 * @version 0.1
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2009-09-28
 * @uses HTMLTag
 */
class Paginator extends AppHelper
{
	public $page = 0;
	
	public $pagesTotal = 0;
	
	public $url;
	
	/**
	 * List of helpers used by pagination helper
	 * @var array(string)
	 */
	public $helpers = array(
		'HTML',
	);
	
	/**
	 * Return a {@link HTMLTag} object that is a A-Tag with a link to the
	 * current page and the optional passed $attributes.
	 * @var string $label
	 * @var array(string) $attributes
	 * @return HTMLTag
	 */
	public function current($label = null, Array $attributes = array()) 
	{
		return $this->page($this->page, $label);
	}
	
	/**
	 * Return a {@link HTMLTag} object that is a A-Tag with a link to $page
	 * if this page exists, with the optional $label and $attributes.
	 * @param integer $page
	 * @param string $label
	 * @param array(string) $attributes
	 * @return HTMLTag
	 */
	public function page($page, $label = null, Array $attributes = array()) 
	{
		if (!$this->hasPage($page)) return false;
		return $this->HTML->link(
			$this->url($page),
			coalesce(@$label, $page),
			$attributes
		);
	}
	
	/**
	 * Returns the url that points to the $page
	 * @param integer $page
	 * @return string
	 */
	public function url($page) 
	{
		return String::substitute($this->url, array('page' => $page, 'controller' => $this->controller->name));
	}
	
	/**
	 * Tests if the $page exists
	 * @param integer $page
	 * @return boolean
	 */
	public function hasPage($page) 
	{
		return ((int) $page <= $this->pagesTotal);
	}
	
	/**
	 * Returns a {@link HTMLTag} link to the first page if it exists.
	 * @param string $label optional alternate link label
	 * @param array(string) $attributes optional additional link attributes
	 * @return HTMLTag
	 */
	public function first($label = null, Array $attributes = array()) 
	{
		return $this->page(1, $label, $attributes);
	}
	
	/**
	 * Return a {@link HTML} tag to the last page if there is any with optional
	 * $label and $attributes.
	 *
	 * @param string $label 
	 * @param Array(string) $attributes 
	 * @return HTMLTag
	 */
	public function last($label = null, Array $attributes = array())
	{
		return $this->page($this->pagesTotal, $label, $attributes);
	}
	
	/**
	 * Tests if there is a previous page to the current one.
	 * @return boolean
	 */
	public function hasPrevious() 
	{
		return ($this->page > 1);
	}
	
	/**
	 * Return a {@link HTMLTag} to the previous page
	 * @param string $label
	 * @param array(string) $attributes
	 * @return HTMLTag
	 */
	public function previous($label = null, Array $attributes = array()) 
	{
		return $this->page($this->page - 1 or 1, $label, $attributes);
	}
	
	/**
	 * Checks if there’s a next page for the current Paginator state
	 * @return boolean
	 */
	public function hasNext() 
	{
		return $this->hasPage($this->page + 1);
	}
	
	/**
	 * Return a {@link HTMLTag} object with a link to the next page
	 * @param string $label optional alternate link label text
	 * @param array(string) $attributes optional additional link attributes
	 * @return HTMLTag
	 */
	public function next($label = null, Array $attributes = array()) 
	{
		return $this->page($this->page + 1, $label, $attributes);
	}
	
	/**
	 * Returns a string with links wrapped into $tags that point to the pages
	 * @param string $tag
	 * @param array(string) $attributes
	 * @param integer $padding 
	 * @return string
	 */
	public function numbers($tag = 'li', Array $attributes = array(), $padding = 2) 
	{
		$numbers = array();
		for($i = 1; $i <= $this->pagesTotal; $i++) {
			if (
				$i == $this->page - $padding - 1 ||
				$i == $this->page + $padding + 1
				) {
				$numbers[] = $this->HTML->tag('li', '…');
				continue;
			}
			if (!(
				$i >= ($this->page - $padding) && $i <= ($this->page + $padding)
				|| $i < $padding + 1
				|| $i > $this->pagesTotal - $padding
				)) {
				continue;
			}
			$a = $attributes;
			if ($i == $this->page) {
				$a['class'] = 'current';
			}
			$numbers[] = $this->HTML->tag('li', $this->page($i, $i, $a));
		}
		return implode(LF, $numbers);
	}
	
	/**
	 * Before-Render Callback that is called from the controller
	 * @return boolean
	 */
	public function beforeRender() 
	{
		$this->page = (int) $this->controller->data['pagination']['page'] or 1;
		$this->pagesTotal = (int) $this->controller->data['pagination']['pagesTotal'];
		$this->url = $this->controller->data['pagination']['url'];
		$this->controller->data->set('Paginator', $this);
		return parent::beforeRender();
	}
}