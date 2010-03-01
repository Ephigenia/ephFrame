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

class_exists('IndexedArray') or require dirname(__FILE__).'/IndexedArray.php';

/**
 * Collection Class
 * 
 * A collection is an indexed array with unique entries. No double values
 * allowed.
 * 
 * As a child class of {@link IndexedArray} this class also supports chaining.
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 16.07.2008
 */
class Collection extends IndexedArray {
	
	/**
	 * Adds one new Item to the beginning of the collection
	 *
	 * @param mixed $v
	 * @return unknown
	 */
	public function prepend($v) {
		parent::prepend($v);
		$this->unique();
		return $this;
	}
	
	/**
	 * Adds one or more $value to the collection.
	 * 
	 * <code>
	 * $c = new Collection();
	 * $c->add('A', 'B', 'C');
	 * // should echo ABC
	 * echo $c;
	 * </code>
	 *
	 * @param mixed $v
	 * @return Collection
	 */
	public function add($value) {
		if (func_num_args() > 1) {
			$args = func_get_args();
			$this->callMethod('add', $args);
		} else {
			if (!$this->contains($value)) {
				parent::add($value);
			}
		}
		$this->unique();
		return $this;
	}
	
	/**
	 * Fills the Collection from the passed $array.
	 *
	 * @param array(mixed) $array
	 * @return Collection
	 */
	public function fromArray(Array $data) {
		parent::fromArray($data);
		$this->unique();
		return $this;
	}
	
	/**
	 * Fills the collection with characters from the passed string.
	 *
	 * @param string $string
	 * @param string $devider
	 * @return Collection
	 */
	public function fromString($string, $devider = null) {
		parent::fromString($string, $devider);
		$this->unique();
		return $this;
	}
	
	public function range($start, $end, $stepSize = 1) {
		parent::range($start, $end, $stepSize);
		$this->unique();
		return $this;
	}
	
	public function offsetSet($index, $value) {
		parent::offsetSet($index, $value);
		$this->unique();
		return $this;
	}

}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class CollectionException extends IndexedArrayException {}