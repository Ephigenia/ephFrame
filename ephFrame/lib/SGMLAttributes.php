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
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

// load classes needed for this one
class_exists('Hash') or require dirname(__FILE__).'/Hash.php';
class_exists('String') or require dirname(__FILE__).'/helper/String.php';

/**
 * SGML Attribute Class, inherited from {@link Hash}
 * 
 * This class stores and renders one SGML Attribute an its values.
 * 
 * <code>
 * 
 * </code>
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 03.07.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @version 0.11
 * @uses String
 */
class SGMLAttributes extends Hash
{	
	public function __get($name) 
	{
		return $this->get($name);
	}
	
	public function __set($name, $value) 
	{
		$this->set($name, $value);
		return $this;
	}
	
	/**
	 * Sets or returns an attribute's value. The function will try to 
	 * return the attribute's value if you pass just the name. If the
	 * attribute is found, the value is returned, otherwise a simple null.
	 * @param string $name
	 * @param string|integer|float $value
	 * @return SGMLAttributes|string|integer|float
	 */
	public function attribute($name, $value = null) 
	{
		if (func_num_args() == 1) {
			return $this->get($name);
		} else {
			return $this->set($name, $value);
		}
	}
	
	/**
	 * add/overwrite an attribute
	 * this method checks for invalid attribute names
	 * @param string $name
	 * @param string|integer $name
	 * @return SGMLAttributes
	 */
	public function set($name, $value = null) 
	{
		// disallow empty names
		if (empty($name)) throw new StringExpectedException();
		// check for invalid SGML attribute name
		if (!$this->validSGMLAttributeName($name)) throw new SGMLAttributesInvalidAttributeNameException();
		return parent::set($name, $value);
	}
	
	/**
	 * Tests if the passed $name is a valid SGML attribute name
	 * - checks for control characters such as line brakes, escapes or null string
	 * - checks for integers or other characters than letters in the first character of the name
	 * @param string $name
	 * @return boolean
	 */
	protected function validSGMLAttributeName($name)
	{
		return (!String::hasControlChars($name) && !preg_match('/^[^a-z]{1}.*$/', $name));
	}
	
	/**
	 * Don't render attributes if no attributes set
	 * @return boolean
	 */
	public function beforeRender() 
	{
		if (count($this) == 0) return false;
		return true;
	}
	
	public function __toString() 
	{
		return $this->render();
	}
	
	/**
	 * Renders the current SGML attributes if there are any set. If no
	 * attributes set false is returned
	 * @return boolean|string
	 */
	public function render() 
	{
		$rendered = '';
		if (!$this->beforeRender()) return $rendered;
		foreach ($this->toArray() as $attributeName => $attributeValue) {
			$attributeValue = $this->renderAttributeValue($attributeValue);
			if (strlen($attributeValue) || in_array($attributeName, array('title', 'alt'))) {
				$rendered .= $this->renderAttributeName($attributeName).'="'.$attributeValue.'" ';
			}
		}
		$rendered = trim($rendered);
		return $this->afterRender($rendered);
	}
	
	/**
	 * Renders the attribute Value. Also takes care of that there are no brakres
	 * in the attribute value
	 * @param string|array $input
	 * @return string
	 */
	public function renderAttributeValue($input) 
	{
		$rendered = (is_array($input)) ? implode(' ',$input) : $input;
		// encode entities
		$rendered = preg_replace('@&(?!(amp;|#\d{2,}))@i', '&amp;', $rendered);
		$rendered = strtr($rendered, array('>' => '&gt;', '<' => '&lt;'));
		$rendered = strtr($rendered, array('"' => '&quot;'));
		$rendered = trim(String::stripBrakes($rendered));
		return $rendered;
	}
	
	/**
	 * @param string $name
	 * @return string 
	 */
	public function renderAttributeName($name) 
	{
		return $name;
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class SGMLAttributesException extends BasicException
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class SGMLAttributesInvalidAttributeNameException extends SGMLAttributesException
{}