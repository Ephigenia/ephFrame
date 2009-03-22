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

class_exists('Component') or require dirname(__FILE__).'/component/Component.php';

/**
 *	Set (or Indexed Array) Class
 * 	
 * 	A set is an indexed array with possible double values that can be
 * 	manipulated.
 * 	
 * 	# {@link http://en.wikipedia.org/wiki/Set_%28computer_science%29}
 *  
 * 	If you want to have always unique values. Check out the {@link Collection}
 * 	class.
 * 
 * 	This class supports chaining:
 * 	<code>
 * 	$s = new Set();
 * 	$s->glue = ',';
 * 	$s->add('second value')->add('first value')->sort();
 * 	// echoes 'first value,second value'
 * 	echo $s;
 * 	</code>
 * 
 * 	Check more examples in the docs for every method of this class.
 * 
 * 	This is partially tested in {@link TestSet}
 * 	
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 17.07.2008
 */
class Set extends Component implements Countable, Renderable, Iterator, ArrayAccess {
	
	/**
	 * 	Stores the data in the Set
	 * 	@var string(mixed)
	 */
	protected $data = array();
	
	/**
	 * 	Glue string that is used when rendereing the set (not used when using
	 * 	{@link implode}
	 * 	@var string
	 */
	public $glue = '';
	
	const SORTMODE_DEFAULT = 2;
	const SORTMODE_HUMANIZED = 4;
	const SORTMODE_KEYS = 8;
	
	/**
	 * 	Set Constructor
	 * 
	 * 	Creates a new Set. Pass an initial array or pass the first element
	 * 	of the set.
	 * 	<code>
	 * 	// valid construction examples:
	 * 	$s = new Set('first', 'second', 'aloha');
	 * 	$s = new Set('firstelement');
	 * 	$s = new Set(array('initial', 'elements'));
	 * 	</code>
	 * 	@param array(string)|mixed $data
	 * 	@return Set
	 */
	public function __construct($data = null) {
		if (func_num_args() > 1) {
			$data = func_get_args();
		}
		if ($data !== null) {
			if (is_array($data)) {
				$this->fromArray($data);
			} elseif (is_object($data) && ($data instanceof Set || $data instanceof ObjectSet)) {
				$this->fromArray($data->toArray());
			} else {
				$this->add($data);
			}
		}
		return $this;
	}
	
	public function reset() {
		$this->data = array();
		return $this;
	}
	
	/**
	 * 	Adds more values to the set.
	 * 	Accepting single and multiple arguments.
	 * 
	 *	<code>
	 * 	// echoes 'Hello my name is mr. ephigenia';
	 * 	$s = new Set();
	 * 	echo $s->add('Hello', 'my', 'name', 'is', 'mr.', 'ephigenia')->implode(' ');
	 * 	</code>
	 * 
	 * 	@param string $val
	 * 	@return Set
	 */
	public function add($val) {
		if (func_num_args() > 1) {
			$args = func_get_args();
			foreach($args as $v) {
				$this->add($v);
			}
		} else {
			$this->data[] = $val;
		}
		return $this;
	}
	
	/**
	 * 	Alias for {@link add}
	 * 
	 * 	<code>
	 * 	// should echo 'Hallo Mr. Coder'
	 * 	$s = new Set();
	 * 	$s->add('Hallo', 'Mr.', 'Coder');
	 * 	echo $s->implode(' ');
	 * 	</code>
	 *
	 * 	@param string $val
	 * 	@return Set
	 */
	public function push($val) {
		$args = func_get_args();
		return $this->callMethod('add', $args);
	}
	
	/**
	 * 	Alias for {@link add}
	 *
	 * 	@param string $val
	 * 	@return Set
	 */
	public function append($val) {
		$args = func_get_args();
		return $this->callMethod('add', $args);
	}
	
	/**
	 *	Append to an existing value, if not exists, create index and value
	 * 	@param integer|string $key
	 * 	@param mixed $value
	 */
	public function appendTo($key, $value) {
		if ($this->hasKey($key)) {
			$this->data[$key] .= $value;
		} else {
			$this->set($key, $value);
		}
		return $this;
	}
	
	/**
	 *	Append values from an array to the Set
	 * 	
	 * 	<code>
	 * 	$set = new Set(0,1,2,3);
	 * 	$set->appendFromArray(array(4,5,6,7));
	 * 	// prints 0,1,2,3,4,5,6,7
	 * 	echo $set->implode(',');
	 * 	</code>
	 * 	
	 * 	@param array(mixed) $array
	 * 	@return string
	 */
	public function appendFromArray(Array $array) {
		foreach($array as $v) $this->append($v);
		return $this;
	}
	
	/**
	 *	Set a specific Index in the {@link Set} to a specific value
	 * 	
	 * 	<code>
	 * 	// should echo 0223;
	 * 	$s = new Set(0,1,2,3);
	 * 	$s->setValue(1,2);
	 * 	echo $s;
	 * 	</code>
	 * 	@param string $val
	 * 	@param mixed $val
	 * 	@return Set
	 */
	public function setValue($index, $val) {
		$this->data[(int) $index] = $val;
		return $this;
	}
	
	/**
	 * 	Alias for {@link setValue}
	 *
	 * 	@param string|integer $key
	 * 	@param mixed $value
	 * 	@return Hash
	 */
	public function set($key, $value = null) {
		return $this->setValue($key, $value);
	}
	
	/**
	 * 	Sorts the set values and returns the Set
	 * 	Use the SORTMODE_* constants of the class to use different sort modes.
	 *
	 * 	@param integer $mode
	 * 	@return Set
	 */
	public function sort($mode = self::SORTMODE_DEFAULT) {
		switch ($mode) {
			case self::SORTMODE_KEYS:
				ksort($this->data);
				break;
			case self::SORTMODE_HUMANIZED:
				natsort($this->data);
				break;
			case self::SORTMODE_DEFAULT:
			default:
				asort($this->data);
				break;
		}
		return $this;
	}
	
	/**
	 * 	Adds numbers from $start to $end to the set and returns the set
	 *
	 * 	<code>
	 * 	// echoes 0,10,20,30,40,50,60,70,80,90,100
	 * 	$s->range(0,100,10);
	 * 	echo $s->implode(',');
	 * 	</code>
	 * 	
	 * 	@param integer $start
	 * 	@param integer $end
	 * 	@param integer $stepSize
	 * 	@return Set
	 */
	public function range($start, $end, $stepSize = 1) {
		$rangeArray = range((int) $start, (int) $end, (int) $stepSize);
		if ($this->count() == 0) {
			$this->data = $rangeArray;
		} else {
			foreach($rangeArray as $v) {
				$this->add($v);
			}
		}
		return $this;
	}
	
	/**
	 * 	Fills the set with characters from $string
	 * 
	 * 	<code>
	 * 	// creates an array with the values: W,ö,r,d,s
	 * 	$s->fromString('Wörds');
	 * 	// creates a Set from a string, with deviders
	 * 	$s->fromString('csv,data,maybe?', ',');
	 * 	</code>
	 *
	 * 	@param string $string
	 * 	@param string $devider optional devider, that is used to split the string
	 * 	@return Set
	 */
	public function fromString($string, $devider = null) {
		if ($devider !== null) {
			$this->fromArray(split($devider, $string));
		} else {
			$this->fromArray(String::each($string));
		}
		return $this;
	}
	
	/**
	 * 	Returns the Set as array
	 *	@return array(mixed)
	 */
	public function toArray() {
		return $this->data;
	}
	
	/**
	 * 	Fills the Set with the passed $data
	 * 	@param array(mixed) $data
	 * 	@return Set
	 */
	public function fromArray(Array $data) {
		$this->data = array_values($data);
		return $this;
	}
	
	/**
	 * 	Adds one or more values to the beginning of the Set.
	 *	If {@link unique} is set to true, the $val is only added if the $val
	 * 	is not in the Set allready.
	 * 	@param mixed $val
	 * 	@return Set
	 */
	public function prepend($val) {
		array_unshift($this->data, $val);
		return $this;
	}
	
	/**
	 * 	Returns the value at the $index position if set or the $default if 
	 * 	$index is not set in the set
	 *
	 * 	@param integer $index
	 * 	@param mixed $default returned if $index was not found as index in Set
	 * 	@return mixed
	 */
	public function get($index, $default = null) {
		if (func_num_args() == 1) {
			return $this->offsetGet($index);
		} else {
			return $this->offsetGet($index, $default);
		}
	}
	
	/**
	 * 	Alias for {@link get}
	 *
	 * 	@param integer $index
	 * 	@param mixed $default returned if $index was not found as index in Set
	 * 	@return mixed
	 */
	public function lookup($index, $default = null) {
		if (func_num_args() == 1) {
			return $this->get($index);
		} else {
			return $this->get($index, $default);
		}
	}
	
	/**
	 * 	Returns a Set of values from the Set matching the passed $regexp
	 * 	<code>
	 * 	// echoes '8'
	 * 	$s = new Set('a', '8', '-');
	 * 	echo $s->match('\d');
	 * 	</code>
	 * 	@param string $regexp
	 * 	@param boolean $invert inverts the logic of the method, returning every value that does not match
	 * 	@return Set
	 */
	public function match($regexp, $invert = false) {
		$a = array();
		foreach($this->data as $v) {
			if (preg_match($regexp, $v) != $invert) {
				$a[] = $v;
			}
		}
		$classname = get_class($this);
		return new $classname($a);
	}
	
	/**
	 * 	Returns the index of the first item in the list that is $v. If nothing
	 * 	found, false is returned.
	 * 	@param mixed $v
	 * 	@return integer
	 */
	public function search($v) {
		return array_search($v, $this->data);
	}
	
	/**
	 * 	Alias for {@link search}
	 *
	 * 	@param mixed $v
	 * 	@return integer
	 */
	public function contains($v) {
		return $this->search($v);
	}
	
	/**
	 * 	Alias for {@link search}
	 *
	 * 	@param mixed $v
	 * 	@return integer
	 */
	public function indexOf($v) {
		return $this->search($v);
	}
	
	/**
	 * 	Returns true if the Set has a $value in it
	 * 	@param mixed $value
	 * 	@return boolean
	 */
	public function hasValue($value) {
		return in_array($value, $this->data);
	}
	
	/**
	 * 	Returns a $count of random values from the Set.
	 * 	@param integer $count
	 * 	@return array(mixed)
	 */
	public function rand($count = 1) {
		$r = array();
		foreach (array_rand($this->data, (int) $count) as $int) {
			$r[] = $this[$i];
		}
		return $r;
	}
	
	/**
	 *	Checks if the hole Set is empty or if the $index of the Set contains
	 * 	something.
	 * 	@param string|integer $index
	 * 	@return boolean
	 */
	public function isEmpty($index = null) {
		// check if value of $index is empty
		if ($index !== null) {
			$val = $this->get($index);
			return empty($val);
		}
		// check if the hole set is empty
		if (count($this) == 0) return true;
		foreach($this as $value) {
			if (!empty($value)) return false;
		}
		return false;
	}
	
	/**
	 * 	Shuffle the values in the set and returns it
	 * 	@return Set
	 */
	public function shuffle() {
		shuffle($this->data);
		return $this;
	}
	
	/**
	 * 	Merges an other Set or array with this set with optional overwriting.
	 *	@param array|Set $array
	 * 	@param boolean $overwrite
	 * 	@return Set
	 */
	public function merge($array, $overwrite = true) {
		if ($array instanceof Set) {
			$array = $array->toArray();
		}
		if ($overwrite) {
			$this->data = array_merge($this->data, $array);
		} else {
			$this->data = array_merge($array, $this->data);
		}
		return $this;
	}
	
	/**
	 * 	Returns a new {@link Set} created from parts of the set starting from $offset
	 * 	with the $length and with optional $preserveKeys.
	 *
	 * 	@param integer $offset
	 * 	@param integer $length
	 * 	@param boolean $preserveKeys
	 * 	@return array(mixed)
	 */
	public function slice($offset, $length = null, $preserveKeys = false) {
		$classname = get_class($this);
		return new $classname(array_slice($this->data, (int) $offset, (int) $length, (bool) $preserveKeys));
	}
	
	/**
	 * 	Returns the first value from the Set without popping it like {@link shift}
	 * 	does. If the Set is empty null is returned
	 * 	@return mixed
	 */
	public function first() {
		if (count($this) == 0) return null;
		$keys = array_keys($this->data);
		return $this->data[$keys[0]];
	}
	
	/**
	 * 	Returns the first element from the Set and pops it off
	 * 	@return mixed
	 */
	public function shift() {
		return array_shift($this->data);
	}
	
	/**
	 * 	Returns the last value from the set without popping it like {@link pop}.
	 * 	If the Set is empty null is returned
	 * 	@return mixed
	 */
	public function last() {
		if (count($this) == 0) return null;
		return $this->data[$this->count()-1];
	}
	
	/**
	 * 	Removes the first element from the Set and cuts it from the Set
	 * 	@return mixed
	 */
	public function pop() {
		return array_pop($this->data);
	}
	
	/**
	 * 	Just like array_map you can set a callback on every value in the Set
	 * 	@param array(string)|string $callback
	 * 	@return Set
	 */
	public function map($callback) {
		$this->data = array_map($callback, $this->data);
		return $this;
	}
	
	/**
	 * 	Just like array_walk you can use a callback on every key(index)/value
	 * 	pair of the Set
	 * 	@param array(string) $callback Valid method call or function call callback
	 * 	@return Set
	 */
	public function walk($callback) {
		$this->data = array_walk($this->data, $callback);
		return $this;
	}
	
	/**
	 * 	Returns the sum of all values in the Set
	 * 	@param integer optional precision if you have some floats around there
	 * 	@return integer|float
	 */
	public function sum($precision = 0) {
		return round(array_sum($this->data), $precision);
	}
	
	/**
	 * 	Returns the product of every value in the Set
	 *	
	 * 	@param integer optional precision if you have some floats around there
	 * 	@return integer|float
	 */
	public function product() {
		return array_product($this->data);
	}
	
	/**
	 * 	Returns an md5 hash of all values in the Set using optional $salt
	 * 	@param string $salt
	 * 	@return string
	 */
	public function hash($salt = '') {
		return md5($this->implode('').$salt);
	}
	
	/**
	 * 	Returns the minimum value of all values in this Set or returns a Set of
	 * 	the $count minimum values of the set ordered by size.
	 * 	@param integer $count Number of minimum values that should be returned
	 * 	@return int|float|string|Set
	 */
	public function min($count = 1) {
		if ($count <= 0) {
			return null;
		} elseif ($count == 1) {
			return call_user_func_array('min', $this->data);
		} elseif ($count >= count($this)) {
			return clone($this);
		} else {
			$cpy = clone($this);
			$minimums = array();
			for ($i = 1; $i <= $count; $i++) {
				$minimum = $cpy->min();
				$cpy->delAll($minimum);
				$minimums[] = $minimum;
			}
			$classname = get_class($this);
			return new $classname($minimums);
		}
	}
	
	/**
	 * 	Returns the maximum value of all values in this Set
	 * 	@param integer $count Number of maximum values that should be returned
	 * 	@return int|float|string|Set
	 */
	public function max($count = 1) {
		if ($count <= 0) {
			return null;
		} elseif ($count == 1) {
			return call_user_func_array('max', $this->data);
		} elseif ($count >= count($this)) {
			return clone($this);
		} else {
			$cpy = clone($this);
			$maximums = array();
			for ($i = 1; $i <= $count; $i++) {
				$maximum = $cpy->max();
				$cpy->delAll($maximum);
				$maximums[] = $maximum;
			}
			$classname = get_class($this);
			return new $classname($maximums);
		}
	}
	
	/**
	 * 	Reverses the values order in the set. All keys are preserved, but you
	 * 	can set $preserveKeys to false to drop the keys.
	 * 	@param boolean $preserveKeys
	 * 	@return Set
	 */
	public function reverse($preserveKeys = true) {
		$this->data = $this->reversed($preserveKeys);
		return $this;
	}
	
	/**
	 * 	Returns a new set with all elements from this set but in reversed order
	 * 	@param boolean $preserveKeys 
	 * 	@return Set
	 */
	public function reversed($preserveKeys = true) {
		$classname = get_class($this);
		return new $classname(array_reverse($this->data, $preserveKeys));
	}
	
	/**
	 * 	Makes every value in the Set unique and returns the Set as array
	 * 	@return Set
	 */
	public function unique() {
		$new = array();
		foreach($this->data as $key => $value) {
			if (!in_array($value, $new)) $new[$key] = $value;
		}
		$this->data = $new;
		return $this;
	}
	
	/**
	 * 	Returns the number of values in the Set
	 *
	 * 	@return integer
	 */
	public function count() {
		return count($this->data);
	}
	
	/**
	 * 	Alias for {@link count}
	 *
	 * 	@return integer
	 */
	public function size() {
		return $this->count();
	}
	
	/**
	 * 	Alias for {@link count}
	 * 	@return integer
	 */
	public function len() {
		return $this->count();
	}
	
	/**
	 * 	Alias for {@link count}
	 * 	@return integer
	 */
	public function length() {
		return $this->count();
	}
	
	/**
	 * 	Returns the current index
	 * 	@return integer
	 */
	public function key() {
		return key($this->data);
	}
	
	/**
	 * 	Returns current element from the Set
	 * 	@return mixed
	 */
	public function current() {
		return current($this->data);
	}
	
	/**
	 * 	Continuously cycles through the elements in this set.
	 * 	
	 * 	<code>
	 * 	// should echo '123450123450123'
	 * 	$set = new Set(0,1,2,3,4,5);
	 * 	for ($i = 0; $i < 15; $i++) {
	 * 		echo $set->cycle();
	 * 	}
	 *	</code>
	 * 
	 * 	With this you can create simple animation arrays:
	 * 	<code>
	 * 	// should echo '-\|/-\|/-\'
	 * 	$progressIndicator = new Set('-', '\\', '|', '/');
	 * 	for($i = 0; $i < 10; $i++) {
	 * 		echo $progressIndicator->cycle();
	 * 	}
	 * 	</code>
	 * 
	 * 	@return mixed
	 */
	public function cycle() {
		$r = $this->current();
		if (!$r) {
			$this->rewind();
			$r = $this->current();
		}
		$this->next();
		return $r;
	}
	
	public function next() {
		return next($this->data);
	}
	
	public function rewind() {
		return reset($this->data);
	}
	
	public function valid() {
		return FALSE !== $this->current();
	}
	
	public function offsetExists($index) {
		return isset($this->data[$index]);
	}
	
	public function hasIndex($index) {
		return $this->offsetExists($index);
	}
	
	public function defined($index) {
		return $this->offsetExists($index);
	}
	
	public function hasKey($index) {
		return $this->hasIndex($index);
	}
	
	public function offsetGet($index) {
		if (isset($this->data[$index])) {
			return $this->data[$index];
		} else {
			return null;
		}
	}
	
	public function offsetSet($index, $value) {
		if ($index === null) {
			$index = $this->count();
		}
		$this->data[$index] = $value;
		return $this;
	}
	
	public function offsetUnset($index) {
		if (isset($this->data[$index])) {
			if (is_int($index)) {
				array_splice($this->data, $index, 1);
	        } else {
				unset($this->data[$index]);
	        }
			return true;
		}
		return false;
	}
	
	/**
	 * 	Removes a specific index from the Set
	 * 	@param integer $index
	 * 	@return boolean success if the index was found and successfully deleted
	 */
	public function del($index) {
		return $this->delete($index);
	}
	
	/**
	 * 	Alias for {@link del}
	 * 	@param integer $index
	 * 	@return Set
	 */
	public function delete($index = null) {
		return $this->offsetUnset($index);
	}
	
	/**
	 * 	Alias for {@link del}
	 * 	@param integer $index
	 * 	@return Set
	 */
	public function remove($index) {
		return $this->del($index);
	}
	
	/**
	 * 	Alias for {@link del}
	 *
	 * 	@param integer $index
	 * 	@return Set
	 */
	public function deleteKey($index) {
		return $this->del($index);
	}
	
	/**
	 * 	Alias for {@link del}
	 *
	 * 	@param integer $index
	 * 	@return Set
	 */
	public function deleteIndex($index) {
		return $this->del($index);
	}
	
	/**
	 * 	Removes all values from the Set with $value.
	 * 	<code>
	 * 	$set = new Set(0,1,2,3,3,4);
	 * 	// should echo 0,1,2,4
	 * 	echo $set->delAll(3)->implode(',');
	 * 	</code>
	 * 	@param mixed
	 * 	@return boolean
	 */
	public function delAll($value) {
		$index = $this->indexOf($value);
		while($index !== false) {
			$this->del($index);
			$index = $this->indexOf($value);
		}
		return $this;
	}
	
	/**
	 * 	Alias for {@link delAll}
	 * 	@param mixed
	 * 	@return boolean
	 */
	public function removeAll($value) {
		return $this->delAll($value);
	}
	
	/**
	 * 	Alias for {@link delAll}
	 * 	@param mixed
	 * 	@return boolean
	 */
	public function deleteAll($value) {
		return $this->delAll($value);
	}
	
	/**
	 * 	Alias for {@link delAll}
	 * 	@param mixed
	 * 	@return boolean
	 */
	public function deleteValue($value) {
		return $this->delAll($value);
	}
	
	/**
	 * 	Cleares the hole Set, removing all items. Always returns an empty Set.
	 * 	@return Set
	 */
	public function clear() {
		$this->data = array();
		return $this;
	}
	
	/**
	 * 	Converts the values to a string using $glue as devider
	 * 	@param string $glue
	 * 	@return string
	 */
	public function implode($glue = '') {
		return implode($glue, $this->data);
	}
	
	/**
	 * 	Returns an array of Keys set in this set
	 * 	@return Set
	 */
	public function keys() {
		$class = get_class($this);
		return new $class(array_keys($this->data));
	}
	
	/**
	 * 	Returns all values in the set as Array.
	 *	@return Set
	 */
	public function values() {
		$class = get_class($this);
		return new $class(array_values($this->data));
	}
	
	/**
	 * 	Returns an array containing key=>value pairs
	 * 	<code>
	 * 	// should echo something like
	 * 	// 0 is 1
	 *  // 1 is 2
	 *  // 2 is 3 ...
	 * 	$s = new Set(1,2,3,4,5);
	 * 	foreach($f->items() as $v) {
	 * 		echo $v[0].' is '.$v[1]."\n";
	 * 	}
	 * 	</code>
	 *	@return array(array(mixed))
	 */
	public function items() {
		$r = array();
		foreach($this as $k => $v) {
			$r[] = array($k, $v);
		}
		return $r;
	}
	
	/**
	 * 	Shortcut/Alias for {@link Array::implodef} Please read the docu of that
	 * 	method for further info and examples
	 *
	 * @param string $glue
	 * @param string $format
	 * @param string|array(string) $keyCallback
	 * @param string|array(string) $valueCallback
	 * @return string
	 */
	public function implodef($glue = '', $format = '', $keyCallback = null, $valueCallback = null) {
		return ArrayHelper::implodef($this->data, $glue, $format, $keyCallback, $valueCallback);
	}
	
	/**
	 * 	Called before {@link render} starts. if this returns false {@link render}
	 * 	will do nothing. You can use this for implement pre-rendering logic.
	 * 	@return string
	 */
	public function beforeRender() {
		return true;
	}
	
	/**
	 * 	Callback called after {@link render} finished. You can use this for
	 * 	manipulating the result from {@link render}
	 *
	 * 	@param string $rendered
	 * 	@return string
	 */
	public function afterRender($rendered) {
		return $rendered;
	}
	
	/**
	 * 	Renders the Set and returns the result
	 * 	@return string
	 */
	public function render() {
		if (!$this->beforeRender()) return '';
		return $this->afterRender($this->implode($this->glue));
	}
	
	/**
	 * 	magic overloading for string casting of this class directed to {@link render}
	 * 	@return string
	 */
	public function __toString() {
		return $this->render();
	}
	
}

/**
 * 	@package ephFrame
 *	@subpackage ephFrame.lib.exception 
 */
class SetException extends BasicException {
}

?>