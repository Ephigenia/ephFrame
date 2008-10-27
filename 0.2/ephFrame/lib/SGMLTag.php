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

require_once dirname(__FILE__).'/Tree.php';
require_once dirname(__FILE__).'/SGMLAttributes.php';

/**
 *	SGML Class
 * 
 * 	base for every {@link http://de.wikipedia.org/wiki/SGML SGML}
 * 	class such as {@link XML}, {@link HTMLTag}, XHTML
 * 
 * 	@todo add xpath functions
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 21.06.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@abstract 
 */
abstract class SGMLTag extends Tree implements Traversable {
	
	const OPEN = '<';
	const CLOSE = '>';
	
	/**
	 * 	Stores the tag’s Name
	 * 	@var string
	 */
	public $tagName;
	
	/**
	 *	Stores the value of this tag
	 * 	@var string|integer|float
	 */
	public $tagValue;
	
	/**
	 * 	Stores the attributes for this tag
	 * 	@var SGMLAttributes
	 */
	public $attributes;
	
	/**
	 *	SGML Tag Constructor
	 * 
	 * 	Create a new SGML Tag, using the $attributes and set the tag value to
	 * 	$tagValue.
	 * 	<code>
	 * 	$tag = new SGMLTag('img', array('src' => '../img/test.jpg');
	 * 	</code>
	 * 
	 * 	@param string $tagName
	 * 	@param array(string) $attributes
	 * 	@param mixed $tagValue
	 */
	public function __construct($tagName = null, $attributes = array(), $tagValue = null) {
		parent::__construct();
		$this->tagName($tagName);
		if (!empty($this->attributes)) {
			$attributes = array_merge($this->attributes, $attributes);
		}
		$this->attributes = new SGMLAttributes($attributes);
		$this->tagValue($tagValue);
		return $this;
	}
	
	/**
	 * 	Sets or returns the name of this SGML Node
	 * 	@param	string $name
	 * 	@return string|SGMLTag
	 */
	public function tagName($name = null) {
		if (!$this->validTagName($name)) throw new SGMLTagInvalidTagNameException();
		return $this->__getOrSet('tagName', strtolower($name));
	}
	
	/**
	 *	Checks if the passed $name is a valid SGML tag name
	 *  - first character needs to be integer or character
	 *  - no open or close tags allowed
	 * 	@param string
	 * 	@return boolean
	 */
	protected function validTagName($name) {
		return (!preg_match('/^[^a-z]{1}.*$/', $name));
	}
	
	/**
	 *	Sets or returns the value of this SGML Tag, value
	 * 	represents the stuff that is between the tag delimeters. Just
	 * 	like 'Hello' here:
	 * 	<code>
	 * 		<tagName>hello</tagName>
	 * 	</code>
	 * 	@param string
	 * 	@return SGMLTag
	 */
	public function tagValue($value = null) {
		if (func_num_args() == 0) return $this->tagValue;
		$this->tagValue = $value;
		return $this;
	}
	
	/**
	 *	Sets or returns an attribute's value
	 * 	@param string $attributeName
	 * 	@return string|integer|array $attributeValue
	 */
	public function attribute($attributeName, $attributeValue = null) {
		if ($attributeValue === null) {
			return $this->getAttribute($attributeName);
		}
		return $this->setAttribute($attributeName, $attributeValue);
	}
	
	/**
	 *	Wrapper function for the {@link SGMLAttributes} Class for this Tag
	 * 	that sets an attribute
	 * 	@param string $attributeName
	 * 	@param string|integer $value
	 * 	@return SGMLTag
	 */
	public function setAttribute($attributeName, $value) {
		$this->attributes->set($attributeName, $value);
		return $this;
	}
	
	/**
	 *	Returns the value of an attribute ... this is an wrapper function
	 * 	for the {@link SGMLAttributes} class that represents the attributes
	 * 	of this tag
	 * 	@param string $attributeName
	 * 	@return string|integer
	 */
	public function getAttribute($attributeName) {
		return $this->attributes->get($attributeName);
	}
	
	/**
	 * 	Removes an attribute
	 * 	@param string $attributeName
	 * 	@return SGMLTag
	 */
	public function removeAttribute($attributeName) {
		$this->attributes->remove($attributeName);
		return $this;
	}
	
	/**
	 *	Iterate of the children and return the first child with the passed 
	 * 	$attributeValue. If no match found false is returned.
	 * 
	 * 	Return the field with 'username' as 'name' attribute:
	 * 	<code>
	 * 	$field = $form->childWithAttribute('name', 'username');
	 * 	</code>
	 * 	
	 * 	@param string $name
	 * 	@param string $value
	 * 	@return SGMLTag|boolean
	 */
	public function childWithAttribute($name, $value = null) {
		foreach ($this->children() as $child) {
			if (isset($child->attributes[$name])) {
				if (func_num_args() == 1) {
					return $child;
				} elseif ($child->attributes[$name] == $value) {
					return $child;
				}
			}
		}
		return false;
	}
	
	/**
	 * 	Renders the SGML Tag
	 * 	@return string
	 */
	public function render($escaped = false) {
		if (!$this->beforeRender()) return false;
		// translate value into sgml readyble format by converting the open and end tags in values to readable
		$value = $this->tagValue;
		// do not render empty tags with no attributes
		if (empty($value) && count($this->attributes) == 0 && !$this->hasChildren()) return $this->afterRender('');
		if (strpos($value, self::OPEN)) $value = str_replace(self::OPEN, htmlentities(self::OPEN), $value);
		if (strpos($value, self::CLOSE)) $value = str_replace(self::CLOSE, htmlentities(self::CLOSE), $value);
		$rendered = $this->renderOpenTag();
		if (!empty($value)) {
			$rendered .= $this->renderValue($escaped);
		}
		if ($this->hasChildren()) {
			$rendered .= LF.$this->tagIndent();
			foreach ($this->children() as $child) {
				$rendered .= $child->render();
			}
		}
		$rendered .= $this->renderCloseTag();
		return $this->afterRender($rendered);
	}
	
	public function __toString() {
		return $this->render();
	}
	
	/**
	 * 	Render SGML Tag value, automatically escapes the value
	 * 	@param boolean $escaped
	 * 	@return string
	 */
	public function renderValue($escaped) {
		if ($escaped) {
			return htmlentities($this->tagValue);
		} else {
			return $this->tagValue;
		}
	}
	
	public function tagIndent() {
		return str_repeat(TAB, $this->level);
	}
	
	/**
	 *	Renders the closing tag for this tag
	 * 	@return string
	 */
	public function renderOpenTag() {
		if (empty($this->tagName)) return '';
		$rendered = $this->tagIndent().self::OPEN.$this->tagName;
		$attributes = $this->attributes->render();
		if (!empty($attributes)) $rendered .= ' '.$attributes;
		$rendered .= self::CLOSE;
		return $rendered;
	}
	
	/**
	 *	Renders the closing tag. If this SGML Tag has no value an empty string
	 * 	is returned because SGML Tags with empty values don't need a closing tag
	 */
	public function renderCloseTag() {
		return $this->tagIndent().self::OPEN.'/'.$this->tagName.self::CLOSE.LF;
	}
	
}

/**
 * 	@package ephFrame
 *	@subpackage ephFrame.lib.exception
 */
class SGMLTagException extends ObjectException { }

/**
 * 	@package ephFrame
 *	@subpackage ephFrame.lib.exception
 */
class SGMLTagInvalidTagNameException extends SGMLTagException {}

?>